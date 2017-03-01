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

class DiagnosticTable
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

	public function hasDiagnostics()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_cie10');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($diagnostic = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		/*$spec = function (Where $where) use ($diagnostic) {
		    $where->like('nom_dia', "%$diagnostic%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_cie10')
		;
		$join->where($spec);
		$sql = $join->order('nom_dia')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;*/

		$sql = "SELECT cod_dia, nom_dia FROM hcneuro_cie10 AS a
				WHERE nom_dia LIKE '%$diagnostic%' OR cod_dia LIKE '%$diagnostic%'
				ORDER BY nom_dia
				LIMIT {$options["limit"]}
				";

		$adapter = $this->dbAdapter;
		$results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

}
