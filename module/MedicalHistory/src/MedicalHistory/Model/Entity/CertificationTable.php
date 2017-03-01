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

class CertificationTable
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

	public function hasCertifications()
	{
		if (is_null($result))
			return count($this->search("", array("limit" => 1)));
		return count($result);
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_certificacion');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($incapacity = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($incapacity) {
		    $where->like('tit_cer', "%$incapacity%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_certificacion')
		;
		$join->where($spec);
		$sql = $join->order('tit_cer')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getCertification($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_cer' => $code));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la certificación con el código $code");
		return $row;
	}

	public function getPatientCertifications($num_doc_pac, $cod_tip_doc, $cod_usu_med = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;
		$medico = (!is_null($cod_usu_med)) ? "AND cod_usu = '$cod_usu_med'" : '';
		$params["cod_tip_doc"] = $cod_tip_doc;
		$params["num_doc_pac"] = $num_doc_pac;

		/*$select = $this->sql->select();
		$join = $select
				->from('hcneuro_certificacion')
		;
		$join->where(array(
				'cod_tip_doc' => $params["cod_tip_doc"],
				'num_doc_pac' => $params["num_doc_pac"]
			)
		);
		$sql = $join->order('fec_reg DESC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;*/

		$sql = "SELECT * FROM  hcneuro_certificacion
				WHERE cod_tip_doc = $cod_tip_doc AND num_doc_pac = '$num_doc_pac' $medico
				LIMIT {$options["limit"]}
				";

		$adapter = $this->dbAdapter;
		$results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $results;

	}

	public function isCertification($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_cer' => $code));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addCertification(Certification $crt)
	{
		$data = array(
			'tit_cer' => $crt->tit_cer,
			'des_cer' => $crt->des_cer,
			'cod_adm' => $crt->cod_adm,
			'cod_tip_doc' => $crt->cod_tip_doc,
			'num_doc_pac' => $crt->num_doc_pac,
			'fec_reg' => $crt->fec_reg,
			'cod_usu' => $crt->cod_usu,
		);

		$adapter = $this->dbAdapter;

		$type = $data["cod_tip_doc"];
		$doc = $data["num_doc_pac"];
		$sql = "SELECT * FROM hcneuro_pacientes WHERE num_doc_pac = '$doc' AND cod_tip_doc = '$type'";
		$check = $this->dbAdapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		if (!count($check))
			throw new \Exception("El paciente no existe!");

		$result = $this->dbAdapter->query("SELECT MAX(cod_cer) AS id FROM hcneuro_certificacion", $adapter::QUERY_MODE_EXECUTE);

		# El código de admision se aumenta con programación (Propuesta de Oscar Galvis)
		foreach ($result as $key => $value) {
			$data['cod_cer'] = ($value->id) + 1;
			break;
		}
		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateCertification(Certification $crt)
	{
		$data = array(
			'tit_cer' => $crt->tit_cer,
			'des_cer' => $crt->des_cer,
			'cod_adm' => $crt->cod_adm,
			'cod_tip_doc' => $crt->cod_tip_doc,
			'num_doc_pac' => $crt->num_doc_pac,
			'fec_reg' => $crt->fec_reg,
			'cod_usu' => $crt->cod_usu,
		);

		$id = (int) $crt->cod_cer;
		$this->tableGateway->update($data, array('cod_cer' => $id));
	}

	public function deleteCertification($id)
	{
		if ($this->isCertification($id))
			$this->tableGateway->delete(array('cod_cer' => (int) $id));
		else
			throw new \Exception("La certificación no existe!");
	}

}
