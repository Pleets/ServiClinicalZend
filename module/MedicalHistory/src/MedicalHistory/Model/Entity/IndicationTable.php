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

class IndicationTable
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

	public function hasIndications()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_indicaciones');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($indication = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($indication) {
		    $where->like('indicacion', "%$indication%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_indicaciones')
		;
		$join->where($spec);
		$sql = $join->order('cod_ind ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getIndication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_ind)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_ind' => $cod_ind));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el indicación con el código $cod_ind");
		return $row;
	}

	public function getPatientIndicationsByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		return $rowset;
	}

	public function deletePatientIndicationByFolio($num_doc_pac, $cod_tip_doc, $num_fol, $cod_ind)
	{
		if ($this->isIndication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_ind))
			$this->tableGateway->delete(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_ind' => $cod_ind));
		else
			throw new \Exception("El indicación no existe!");
	}

	public function isIndication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_ind)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_ind' => $cod_ind));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addIndication(Indication $ind)
	{
		$data = array(
			'cod_tip_doc' => $ind->cod_tip_doc,
			'num_doc_pac' => $ind->num_doc_pac,
			'num_fol' => $ind->num_fol,
			'cod_ind' => $ind->cod_ind,
			'indicacion' => $ind->indicacion,
		);

		$indication = $ind->cod_ind;

		if ($this->isIndication($ind->num_doc_pac, $ind->cod_tip_doc, $ind->num_fol, $ind->cod_ind))
			throw new \Exception("Ya existe el indicación $indication en la admisión");

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateIndication(Indication $ind)
	{
		$data = array(
			'indicacion' => $ind->indicacion,
		);

		$indication = $ind->cod_ind;

		$type = $ind->cod_tip_doc;
		$doc = $ind->num_doc_pac;
		$folio = $ind->num_fol;

		if (!$this->isIndication($ind->num_doc_pac, $ind->cod_tip_doc, $ind->num_fol, $ind->cod_ind))
			throw new \Exception("No existe el indicación $indication en la admisión");

		$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio, 'cod_ind' => $indication));

		return $data;
	}

}
