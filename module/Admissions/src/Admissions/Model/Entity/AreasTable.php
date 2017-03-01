<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class AreasTable
{
	private $sql;
	private $dbAdapter;
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway, \Zend\Db\Adapter\Adapter $dbAdapter)
	{
		$this->tableGateway = $tableGateway;
		$this->sql = new Sql($dbAdapter);
		$this->dbAdapter = $dbAdapter;
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select ->from('hcneuro_areas');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($nom = "")
	{
		$spec = function (Where $where) use ($nom) {
		    $where->like('nom_are', "%$nom%");
		};
		$select = $this->sql->select();
		$join = $select->from('hcneuro_areas');
		$join->where($spec);
		$sql = $join->order('cod_are ASC')->limit(100);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getArea($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_area' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isArea($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_are' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addArea(Area $area)
	{
		$data = array(
			'cod_are' => $area->cod_are,
			'nom_are' => $area->nom_are,
			'est_are' => $area->est_are,
		);

		if ($this->isArea($area->cod_are))
			throw new \Exception("Ya existe un área con este código");
		else
			$this->tableGateway->insert($data);
	}

	public function updateArea(Area $area)
	{
		$data = array(
			'nom_are' => $area->nom_are,
			'est_are' => $area->est_are,
		);

		$id = (int) $doc->cod_are;
		$this->tableGateway->update($data, array('cod_are' => $id));
	}

	public function deleteArea($id)
	{
		if ($this->isArea($id))
			$this->tableGateway->delete(array('cod_are' => (int) $id));
		else
			throw new \Exception("El área no existe!");
	}
}
