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

class MedicationsTable
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
		$sql = $select ->from('hcneuro_medicamentos');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($med = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($med) {
		    $where->like('nom_med', "%$med%");
		};
		$select = $this->sql->select();
		$join = $select->from('hcneuro_medicamentos');
		$join->where($spec);
		$sql = $join->order('cod_med ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getMedication($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_med' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isMedication($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_med' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isMedicationName($nom_med)
	{
		$rowset = $this->tableGateway->select(array('nom_med' => $nom_med));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addMedication(Medication $med)
	{
		$data = array(
			'nom_med' => $med->nom_med,
			'nom_gen' => $med->nom_gen,
			'con_med' => $med->con_med,
			'pre_med' => $med->pre_med,
			'est_med' => $med->est_med,
		);

		if ($this->isMedicationName($med->nom_med))
			throw new \Exception("Ya existe un medicamento con este nombre!");
		else
		{
			$adapter = $this->dbAdapter;
			$result = $this->dbAdapter->query("SELECT MAX(cod_med) AS id FROM hcneuro_medicamentos", $adapter::QUERY_MODE_EXECUTE);

			# El código de medicamento se aumenta con programación (Propuesta de Oscar Galvis)
			foreach ($result as $key => $value) {
				$data['cod_med'] = ($value->id) + 1;
				break;
			}
			$this->tableGateway->insert($data);
		}
	}

	public function updateMedication(Medication $med)
	{
		$data = array(
			'nom_med' => $med->nom_med,
			'nom_gen' => $med->nom_gen,
			'con_med' => $med->con_med,
			'pre_med' => $med->pre_med,
		);

		$id = (int) $med->cod_med;

		if ($thisUser = $this->getMedication($id)) {
			if (!$this->isMedicationName($med->nom_med) || strtolower($thisUser->nom_med) == strtolower($med->nom_med))
				$this->tableGateway->update($data, array('cod_med' => $id));
			else
				throw new \Exception("Ya existe un medicamento con este nombre!");
		}
	}

	public function deleteMedication($id)
	{
		if ($this->isMedication($id))
			$this->tableGateway->delete(array('cod_med' => (int) $id));
		else
			throw new \Exception("El medicamento no existe!");
	}

	public function disableMedication($id)
	{
		$this->tableGateway->update(
			array('est_med' => 2),
			array('cod_med' => $id)
		);
	}

	public function enableMedication($id)
	{
		$this->tableGateway->update(
			array('est_med' => 1),
			array('cod_med' => $id)
		);
	}


	public function countMedications()
	{
		$sql = "SELECT COUNT(cod_med) AS total FROM hcneuro_medicamentos";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

}
