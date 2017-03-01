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

class PatientDiagnosticTable
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
				->from('hcneuro_hcdiapac');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($diagnostic = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($diagnostic) {
		    $where->like('cod_dia', "%$diagnostic%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_hcdiapac')
		;
		$join->where($spec);
		$sql = $join->order('cod_dia')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getDiagnostic($num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_dia' => $cod_dia));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el diagnóstico con el código $cod_dia");
		return $row;
	}

	public function getPatientDiagnosticsByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		return $rowset;
	}

	public function deletePatientDiagnosticByFolio($num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia)
	{
		if ($this->isDiagnostic($num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia))
			$this->tableGateway->delete(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_dia' => $cod_dia));
		else
			throw new \Exception("El diagnóstico no existe!");
	}

	public function isDiagnostic($num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_dia' => $cod_dia));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addDiagnostic(PatientDiagnostic $dia)
	{
		$data = array(
			'cod_tip_doc' => $dia->cod_tip_doc,
			'num_doc_pac' => $dia->num_doc_pac,
			'num_fol' => $dia->num_fol,
			'cod_dia' => $dia->cod_dia,
			'dia_pri' => $dia->dia_pri,
			'tip_dia' => $dia->tip_dia,
			'clasi_dia' => $dia->clasi_dia,
			'clase_dia' => $dia->clase_dia,
			'dia_ing' => $dia->dia_ing,
			'dia_egr' => $dia->dia_egr,
			'obs_dia' => $dia->obs_dia,
		);

		$diagnostic = $dia->cod_dia;

		if ($this->isDiagnostic($dia->num_doc_pac, $dia->cod_tip_doc, $dia->num_fol, $dia->cod_dia))
			throw new \Exception("Ya existe el diagnóstico $diagnostic en la admisión");

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateDiagnostic(PatientDiagnostic $dia)
	{
		$data = array(
			'dia_pri' => $dia->dia_pri,
			'tip_dia' => $dia->tip_dia,
			'clasi_dia' => $dia->clasi_dia,
			'clase_dia' => $dia->clase_dia,
			'dia_ing' => $dia->dia_ing,
			'dia_egr' => $dia->dia_egr,
			'obs_dia' => $dia->obs_dia,
		);

		$diagnostic = $dia->cod_dia;

		$type = $dia->cod_tip_doc;
		$doc = $dia->num_doc_pac;
		$folio = $dia->num_fol;

		if (!$this->isDiagnostic($dia->num_doc_pac, $dia->cod_tip_doc, $dia->num_fol, $dia->cod_dia))
			throw new \Exception("No existe el diagnóstico $diagnostic en la admisión");

		$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio, 'cod_dia' => $diagnostic));

		return $data;
	}

}
