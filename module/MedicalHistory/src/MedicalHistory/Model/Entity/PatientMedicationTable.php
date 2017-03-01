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

class PatientMedicationTable
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

	public function hasMedications()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_hcmedfor');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($medication = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($medication) {
		    $where->like('cod_med', "%$medication%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_hcmedfor')
		;
		$join->where($spec);
		$sql = $join->order('cod_med')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getMedication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_med)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_med' => $cod_med));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el medicamento con el código $cod_med");
		return $row;
	}

	public function getPatientMedicationsByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		return $rowset;
	}

	public function deletePatientMedicationByFolio($num_doc_pac, $cod_tip_doc, $num_fol, $cod_med)
	{
		if ($this->isMedication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_med))
			$this->tableGateway->delete(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_med' => $cod_med));
		else
			throw new \Exception("El medicamento no existe!");
	}

	public function isMedication($num_doc_pac, $cod_tip_doc, $num_fol, $cod_med)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_med' => $cod_med));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addMedication(PatientMedication $med)
	{
		$data = array(
			'cod_tip_doc' => $med->cod_tip_doc,
			'num_doc_pac' => $med->num_doc_pac,
			'num_fol' => $med->num_fol,
			'cod_med' => $med->cod_med,
			'cod_apl_med' => $med->cod_apl_med,
			'can_med' => $med->can_med,
			'pos_med' => $med->pos_med,
			'ter_med' => $med->ter_med,
			'num_dia' => $med->num_dia,
		);

		$medication = $med->cod_med;

		if ($this->isMedication($med->num_doc_pac, $med->cod_tip_doc, $med->num_fol, $med->cod_med))
			throw new \Exception("Ya existe el medicamento $medication en la admisión");

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateMedication(PatientMedication $med)
	{
		$data = array(
			'cod_apl_med' => $med->cod_apl_med,
			'can_med' => $med->can_med,
			'pos_med' => $med->pos_med,
			'ter_med' => $med->ter_med,
			'num_dia' => $med->num_dia,
		);

		$medication = $med->cod_med;

		$type = $med->cod_tip_doc;
		$doc = $med->num_doc_pac;
		$folio = $med->num_fol;

		if (!$this->isMedication($med->num_doc_pac, $med->cod_tip_doc, $med->num_fol, $med->cod_med))
			throw new \Exception("No existe el medicamento $medication en la admisión");

		$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio, 'cod_med' => $medication));

		return $data;
	}

}
