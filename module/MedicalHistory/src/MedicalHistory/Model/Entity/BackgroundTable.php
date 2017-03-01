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

class BackgroundTable
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

	public function hasBackgrounds()
	{
		if (is_null($result))
			return count($this->search("", array("limit" => 1)));
		return count($result);
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_antecedentes');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($incapacity = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($incapacity) {
		    $where->like('des_ant', "%$incapacity%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_antecedentes')
		;
		$join->where($spec);
		$sql = $join->order('des_ant')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getBackground($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_ant' => $code));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar los antecedentes con el código $code");
		return $row;
	}

	public function getPatientBackgrounds($num_doc_pac, $cod_tip_doc, $cod_usu_med = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$params["cod_tip_doc"] = $cod_tip_doc;
		$params["num_doc_pac"] = $num_doc_pac;
		$params["usu_reg"] = $cod_usu_med;

		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_antecedentes')
		;

		if (!is_null($cod_usu_med))
			$join->where(array(
					'cod_tip_doc' => $params["cod_tip_doc"],
					'num_doc_pac' => $params["num_doc_pac"],
					'usu_reg' => $params["usu_reg"]
				)
			);
		else
			$join->where(array(
					'cod_tip_doc' => $params["cod_tip_doc"],
					'num_doc_pac' => $params["num_doc_pac"],
				)
			);

		$sql = $join->order('fec_reg DESC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function isBackground($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_ant' => $code));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addBackground(Background $back)
	{
		$data = array(
			'cod_tip_doc' => $back->cod_tip_doc,
			'num_doc_pac' => $back->num_doc_pac,
			'fec_reg' => $back->fec_reg,
			'usu_reg' => $back->usu_reg,
			'tip_ant' => $back->tip_ant,
			'des_ant' => $back->des_ant,
		);

		$adapter = $this->dbAdapter;

		$type = $data["cod_tip_doc"];
		$doc = $data["num_doc_pac"];
		$sql = "SELECT * FROM hcneuro_pacientes WHERE num_doc_pac = '$doc' AND cod_tip_doc = '$type'";
		$check = $this->dbAdapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		if (!count($check))
			throw new \Exception("El paciente no existe!");

		$result = $this->dbAdapter->query("SELECT MAX(cod_ant) AS id FROM hcneuro_antecedentes", $adapter::QUERY_MODE_EXECUTE);

		# El código de admision se aumenta con programación (Propuesta de Oscar Galvis)
		foreach ($result as $key => $value) {
			$data['cod_ant'] = ($value->id) + 1;
			break;
		}

		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateBackground(Background $back)
	{
		$data = array(
			'cod_tip_doc' => $back->cod_tip_doc,
			'num_doc_pac' => $back->num_doc_pac,
			'usu_reg' => $back->usu_reg,
			'tip_ant' => $back->tip_ant,
			'des_ant' => $back->des_ant,
		);

		$id = (int) $back->cod_ant;
		$this->tableGateway->update($data, array('cod_ant' => $id));
	}

	public function deleteBackground($id)
	{
		if ($this->isBackground($id))
			$this->tableGateway->delete(array('cod_ant' => (int) $id));
		else
			throw new \Exception("El antecedente no existe!");
	}

}
