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

class ObservationTable
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

	public function hasObservations()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_histo_obser')
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getObservation($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el folio $folio para el paciente $id");
		return $row;
	}

	public function isObservation($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addObservation(Observation $history)
	{
		$data = array(
			'cod_tip_doc' => $history->cod_tip_doc,
			'num_doc_pac' => $history->num_doc_pac,
			'num_fol' => $history->num_fol,
			'obs_med' => $history->obs_med,
		);

		$patient = $data["num_doc_pac"];
		$patient = $data["num_fol"];

		if ($this->isObservation($history->num_doc_pac, $history->cod_tip_doc, $history->num_fol))
			throw new \Exception("Ya existe una observación para el paciente $patient con número de folio $folio");
		else {
			try {
			$result = $this->tableGateway->insert($data);
			} catch (\Exception $e) {
				var_dump($e);
			}
		}
	}

	public function updateObservation(Observation $history)
	{
		$data = array(
			'obs_med' => $history->obs_med,
		);

		$this->tableGateway->update($data, array('num_doc_pac' => $history->num_doc_pac, 'cod_tip_doc' => $history->cod_tip_doc, 'num_fol' => $history->num_fol));
	}

	public function deleteObservation($id, $type, $folio)
	{
		if ($this->isObservation($id, $type, $folio))
			$this->tableGateway->delete(array('num_doc_pac' => (string) $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		else
			throw new \Exception("La observación no existe");
	}
}
