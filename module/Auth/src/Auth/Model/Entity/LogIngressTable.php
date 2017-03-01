<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class LogIngressTable
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
		$sql = $select
				->from('log_ingresos')
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function addIngress(LogIngress $user)
	{
		$data = array(
			'cod_usu' => $user->cod_usu,
			'fec_ing' => date('Y-m-d H:i:s'),
			'ip_address' => $user->ip_address,
		);

		$this->tableGateway->insert($data);
	}


	public function countIncome($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM log_ingresos
				WHERE cod_usu = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

}
