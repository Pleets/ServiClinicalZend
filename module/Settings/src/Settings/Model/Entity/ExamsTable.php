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

class ExamsTable
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
		$sql = $select ->from('hcneuro_examenes');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($exam = "", $options = null)
	{
		$options["limit"] = $limit = isset($options["limit"]) ? $options["limit"]: 30;
		$type = $options["type"] = isset($options["type"]) ? "AND tip_exa = '".$options["type"]."'" : '';

		/*$spec = function (Where $where) use ($exam, $type) {
		    $where->like('nom_exa', "%$exam%");
		    if (!is_null($type))
		    	$where->like('tip_exa', $type);
		};
		$select = $this->sql->select();
		$join = $select->from('hcneuro_examenes');
		$join->where($spec);
		$sql = $join->order('cod_exa ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;*/

		$sql = "SELECT * FROM hcneuro_examenes
				WHERE (cod_exa LIKE '%$exam%' OR nom_exa LIKE '%$exam%') $type
				LIMIT $limit
				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset;
	}

	public function getExam($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_exa' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isExam($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_exa' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isExamName($nom_exa)
	{
		$rowset = $this->tableGateway->select(array('nom_exa' => $nom_exa));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addExam(Exam $exam)
	{
		$data = array(
			'cod_exa' => $exam->cod_exa,
			'nom_exa' => $exam->nom_exa,
			'est_exa' => $exam->est_exa,
			'tip_exa' => $exam->tip_exa,
		);

		if ($this->isExam($exam->cod_exa))
			throw new \Exception("Ya existe un exámen con este código");
		if ($this->isExamName($exam->nom_exa))
			throw new \Exception("Ya existe un exámen con este nombre!");
		else
			$this->tableGateway->insert($data);
	}

	public function updateExam(Exam $exam)
	{
		$data = array(
			'nom_exa' => $exam->nom_exa,
			'tip_exa' => $exam->tip_exa,
		);

		$id = (string) $exam->cod_exa;
		if ($thisUser = $this->getExam($id)) {
			if (!$this->isExamName($exam->nom_exa) || strtolower($thisUser->nom_exa) == strtolower($exam->nom_exa))
				$this->tableGateway->update($data, array('cod_exa' => $id));
			else
				throw new \Exception("Ya existe un exámen con este nombre!");
		}
	}

	public function deleteExam($id)
	{
		if ($this->isExam($id))
			$this->tableGateway->delete(array('cod_exa' => (string) $id));
		else
			throw new \Exception("El exámen no existe!");
	}

	public function disableExam($id)
	{
		$this->tableGateway->update(
			array('est_exa' => 2),
			array('cod_exa' => $id)
		);
	}

	public function enableExam($id)
	{
		$this->tableGateway->update(
			array('est_exa' => 1),
			array('cod_exa' => $id)
		);
	}

	public function countExams()
	{
		$sql = "SELECT COUNT(cod_exa) AS total FROM hcneuro_examenes";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

}
