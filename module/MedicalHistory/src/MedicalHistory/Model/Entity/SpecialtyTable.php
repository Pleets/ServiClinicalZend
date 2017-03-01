<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Model\Entity;

use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class SpecialtyTable
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

	public function hasSpecialties()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_especialidades');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($specialty = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($specialty) {
		    $where->like('nom_esp', "%$specialty%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_especialidades')
		;
		$join->where($spec);
		$sql = $join->order('nom_esp')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getSpecialty($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_esp' => $code));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el diagnóstico con el código $code");
		return $row;
	}

	public function isSpecialty($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_esp' => $code));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}
}
