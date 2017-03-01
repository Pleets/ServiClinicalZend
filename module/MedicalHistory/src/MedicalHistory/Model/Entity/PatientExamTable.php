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

class PatientExamTable
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

	public function hasExams()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_hcexapac');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($exam = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($exam) {
		    $where->like('cod_exa', "%$exam%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_hcexapac')
		;
		$join->where($spec);
		$sql = $join->order('cod_exa')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getExam($num_doc_pac, $cod_tip_doc, $num_fol, $cod_exa)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_exa' => $cod_exa));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el exámen con el código $cod_exa");
		return $row;
	}

	public function getPatientExamsByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		return $rowset;
	}

	public function deletePatientExamByFolio($num_doc_pac, $cod_tip_doc, $num_fol, $cod_exa)
	{
		if ($this->isExam($num_doc_pac, $cod_tip_doc, $num_fol, $cod_exa))
			$this->tableGateway->delete(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_exa' => $cod_exa));
		else
			throw new \Exception("El exámen no existe!");
	}

	public function isExam($num_doc_pac, $cod_tip_doc, $num_fol, $cod_exa)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_exa' => $cod_exa));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addExam(PatientExam $exa)
	{
		$data = array(
			'cod_tip_doc' => $exa->cod_tip_doc,
			'num_doc_pac' => $exa->num_doc_pac,
			'num_fol' => $exa->num_fol,
			'cod_exa' => $exa->cod_exa,
			'can_exa' => $exa->can_exa,
			'est_exa' => $exa->est_exa,
			'obs_exa' => $exa->obs_exa,
			'tip_exa' => $exa->tip_exa,
		);

		$exam = $exa->cod_exa;

		if ($this->isExam($exa->num_doc_pac, $exa->cod_tip_doc, $exa->num_fol, $exa->cod_exa))
			throw new \Exception("Ya existe el exámen $exam en la admisión");

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateExam(PatientExam $exa)
	{
		$data = array(
			'can_exa' => $exa->can_exa,
			'est_exa' => $exa->est_exa,
			'obs_exa' => $exa->obs_exa,
			'tip_exa' => $exa->tip_exa,
		);

		$exam = $exa->cod_exa;

		$type = $exa->cod_tip_doc;
		$doc = $exa->num_doc_pac;
		$folio = $exa->num_fol;

		if (!$this->isExam($exa->num_doc_pac, $exa->cod_tip_doc, $exa->num_fol, $exa->cod_exa))
			throw new \Exception("No existe el exámen $exam en la admisión");

		$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio, 'cod_exa' => $exam));

		return $data;
	}

}
