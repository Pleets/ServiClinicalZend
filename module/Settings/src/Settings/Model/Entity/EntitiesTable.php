<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Settings\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class EntitiesTable
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

	public function hasEntities()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select ->from('hcneuro_entidad');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($ent = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($ent) {
		    $where->like('nom_ent', "%$ent%");
		};
		$select = $this->sql->select();
		$join = $select->from('hcneuro_entidad');
		$join->where($spec);
		$sql = $join->order('cod_ent ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getEntity($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_ent' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isEntity($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_ent' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isEntityName($nom_ent)
	{
		$rowset = $this->tableGateway->select(array('nom_ent' => $nom_ent));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addEntity(Entity $entity)
	{
		$data = array(
			'cod_ent' => $entity->cod_ent,
			'nom_ent' => $entity->nom_ent,
			'dir_ent' => $entity->dir_ent,
			'est_ent' => $entity->est_ent,
		);

		if ($this->isEntity($entity->cod_ent))
			throw new \Exception("Ya existe una entidad con este código");
		if ($this->isEntityName($entity->nom_ent))
			throw new \Exception("Ya existe una entidad con este nombre!");
		else
			$this->tableGateway->insert($data);
	}

	public function updateEntity(Entity $entity)
	{
		$data = array(
			'nom_ent' => $entity->nom_ent,
			'dir_ent' => $entity->dir_ent,
		);

		$id = (string) $entity->cod_ent;
		if ($thisUser = $this->getEntity($id)) {
			if (!$this->isEntityName($entity->nom_ent) || strtolower($thisUser->nom_ent) == strtolower($entity->nom_ent))
				$this->tableGateway->update($data, array('cod_ent' => $id));
			else
				throw new \Exception("Ya existe una entidad con este nombre!");
		}
	}

	public function deleteEntity($id)
	{
		if ($this->isEntity($id))
			$this->tableGateway->delete(array('cod_ent' => (string) $id));
		else
			throw new \Exception("La entidad no existe!");
	}

	public function disableEntity($id)
	{
		$this->tableGateway->update(
			array('est_ent' => 2),
			array('cod_ent' => $id)
		);
	}

	public function enableEntity($id)
	{
		$this->tableGateway->update(
			array('est_ent' => 1),
			array('cod_ent' => $id)
		);
	}

	public function countEntities()
	{
		$sql = "SELECT COUNT(cod_ent) AS total FROM hcneuro_entidad";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

}
