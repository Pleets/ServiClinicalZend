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

class IncapacityTable
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

	public function hasIncapacties()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_incapacidad');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($incapacity = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($incapacity) {
		    $where->like('nom_esp', "%$incapacity%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_incapacidad')
		;
		$join->where($spec);
		$sql = $join->order('nom_esp')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getIncapacity($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la incapacidad");
		return $row;
	}

	public function isIncapacity($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addIncapacity(Incapacity $inc)
	{
		$data = array(
			'cod_tip_doc' => $inc->cod_tip_doc,
			'num_doc_pac' => $inc->num_doc_pac,
			'num_fol' => $inc->num_fol,
			'fec_ini_inc' => $inc->fec_ini_inc,
			'num_dia_inc' => $inc->num_dia_inc,
			'des_inc' => $inc->des_inc,
			'cod_dia' => $inc->cod_dia,
		);

		if ($this->isIncapacity($inc->num_doc_pac, $inc->cod_tip_doc, $inc->num_fol))
			throw new \Exception("Ya existe la incapacidad en este folio");

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateIncapacity(Incapacity $inc)
	{
		$data = array(
			'fec_ini_inc' => $inc->fec_ini_inc,
			'num_dia_inc' => $inc->num_dia_inc,
			'des_inc' => $inc->des_inc,
			'cod_dia' => $inc->cod_dia,
		);

		$type = $inc->cod_tip_doc;
		$doc = $inc->num_doc_pac;
		$folio = $inc->num_fol;

		if (!$this->isIncapacity($inc->num_doc_pac, $inc->cod_tip_doc, $inc->num_fol))
			throw new \Exception("No existe la incapacidad en el folio");

		try {
			$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio));
		} catch(\Exception $e) {
			var_dump($e);
		}

		return $data;
	}

	public function deleteIncapacityByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		if ($this->isIncapacity($num_doc_pac, $cod_tip_doc, $num_fol))
			$this->tableGateway->delete(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		else
			throw new \Exception("La incapacidad no existe!");
	}
}
