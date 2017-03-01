<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Model\Entity;

use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class AdmissionsTable
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

	public function hasAdmissions($result = null)
	{
		if (is_null($result))
			return count($this->search("", array("limit" => 1)));
		return count($result);
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select ->from('hcneuro_admision');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($needle = "", $options = null)
	{
		$options["limit"] = $limit = isset($options["limit"]) ? $options["limit"]: 2;
		$options["notLimit"] = $notLimit = isset($options["notLimit"]) ? $options["notLimit"]: false;
		$options["onlyToday"] = $today = isset($options["onlyToday"]) ? $options["onlyToday"]: false;
		$options["beforeToday"] = $beforeToday = isset($options["beforeToday"]) ? $options["beforeToday"]: false;
		$options["type"] = $type = isset($options["type"]) ? $options["type"]: null;
		$options["id"] = $id = isset($options["id"]) ? $options["id"]: null;

		$options["cod_usu_med"] = $cod_usu_med = isset($options["cod_usu_med"]) ? $options["cod_usu_med"]: null;
		$doctor = (!is_null($cod_usu_med)) ? "f.cod_usu = '$cod_usu_med' AND": "";

		$limit = (!$limit) ? "LIMIT ".$limit : null;
		$today = ($today) ? "AND LEFT(a.fec_adm, 10) = CURDATE()" : null;
		$beforeToday = ($beforeToday) ? "LEFT(a.fec_adm,10) < CURDATE() AND" : null;

		$where = (!is_null($id) && !is_null($type)) ? "(a.num_doc_pac = '$id' AND d.cod_tip_doc = '$type') AND": "";

		$sql = "SELECT cod_adm, fec_adm, est_adm, nom_tip_doc, a.num_doc_pac,
				CONCAT(pri_nom_pac, ' ', seg_nom_pac, ' ', pri_ape_pac, ' ', seg_ape_pac) AS nom_pac, nom_are, nom_ent,
				obs_adm, f.nom_usu AS usu_med, f.cod_usu AS cod_usu_med, e.nom_usu AS usu_reg, e.cod_usu AS cod_usu_reg
				FROM hcneuro_admision AS a
				INNER JOIN hcneuro_areas AS b ON a.cod_are = b.cod_are
				INNER JOIN hcneuro_entidad AS c ON a.cod_ent = c.cod_ent
				INNER JOIN hcneuro_tipodoc AS d ON a.cod_tip_doc = d.cod_tip_doc
				INNER JOIN hcneuro_usuarios AS e ON a.cod_usu_reg = e.cod_usu
				INNER JOIN hcneuro_usuarios AS f ON a.cod_usu_med = f.cod_usu
				INNER JOIN hcneuro_pacientes AS g ON a.num_doc_pac = g.num_doc_pac AND a.cod_tip_doc = g.cod_tip_doc
				WHERE $where $beforeToday $doctor
				( cod_adm LIKE '%$needle%' OR a.num_doc_pac LIKE '%$needle%'
				OR pri_nom_pac LIKE '%$needle%' OR seg_nom_pac LIKE '%$needle%' OR pri_ape_pac LIKE '%$needle%' OR seg_ape_pac LIKE '%$needle%'
				OR f.nom_usu LIKE '%$needle%' OR f.cod_usu LIKE '%$needle%' ) $today
				ORDER BY cod_adm DESC, fec_adm
				$limit
				";

		$adapter = $this->dbAdapter;
		$results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getFullAdmission($id)
	{
		$sql = "SELECT cod_adm, fec_adm, est_adm, nom_tip_doc, a.num_doc_pac, a.cod_tip_doc,
				CONCAT(pri_nom_pac, ' ', seg_nom_pac, ' ', pri_ape_pac, ' ', seg_ape_pac) AS nom_pac, nom_are, nom_ent,
				dir_pac, fec_nac_pac, num_tel_pac,
				obs_adm, f.nom_usu AS usu_med, f.cod_usu AS cod_usu_med, e.nom_usu AS usu_reg, e.cod_usu AS cod_usu_reg
				FROM hcneuro_admision AS a
				INNER JOIN hcneuro_areas AS b ON a.cod_are = b.cod_are
				INNER JOIN hcneuro_entidad AS c ON a.cod_ent = c.cod_ent
				INNER JOIN hcneuro_tipodoc AS d ON a.cod_tip_doc = d.cod_tip_doc
				INNER JOIN hcneuro_usuarios AS e ON a.cod_usu_reg = e.cod_usu
				INNER JOIN hcneuro_usuarios AS f ON a.cod_usu_med = f.cod_usu
				INNER JOIN hcneuro_pacientes AS g ON a.num_doc_pac = g.num_doc_pac AND a.cod_tip_doc = g.cod_tip_doc
				WHERE a.cod_adm = $id
				LIMIT 1
				";

		$adapter = $this->dbAdapter;
		$result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		$row = $result->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la admisión $id");
		return $row;
	}

	public function getAdmission($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_adm' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la admisión $id");
		return $row;
	}

	public function getPatientAdmissions($id, $type)
	{
		$type = (int) $type;
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $type, 'num_doc_pac' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("El paciente $id no tiene admisiones");
		return $rowset;
	}

	public function getActivePatientAdmissions($id, $type)
	{
		$type = (int) $type;
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $type, 'num_doc_pac' => $id, 'est_adm' => ''));
		return $rowset;
	}

	public function isAdmission($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_adm' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addAdmission(Admission $adm)
	{
		$data = array(
			'fec_adm' => date('Y-m-d H:i:s'),
			'est_adm' => '',
			'cod_tip_doc' => $adm->cod_tip_doc,
			'num_doc_pac' => $adm->num_doc_pac,
			'cod_usu_med' => $adm->cod_usu_med,
			'cod_usu_reg' => $adm->cod_usu_reg,
			'cod_are' => $adm->cod_are,
			'cod_ent' => $adm->cod_ent,
			'obs_adm' => $adm->obs_adm,
		);

		$adapter = $this->dbAdapter;

		$type = $data["cod_tip_doc"];
		$doc = $data["num_doc_pac"];
		$sql = "SELECT * FROM hcneuro_pacientes WHERE num_doc_pac = '$doc' AND cod_tip_doc = '$type'";
		$check1 = $this->dbAdapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		$sql = "SELECT * FROM hcneuro_admision WHERE num_doc_pac = '$doc' AND cod_tip_doc = '$type' and est_adm = '' ";
		$check2 = $this->dbAdapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		if (!count($check1))
			throw new \Exception("El paciente no existe!", 4459);
		elseif (count($check2))
			throw new \Exception("El paciente ya tiene una admisión abierta!");

		$result = $this->dbAdapter->query("SELECT MAX(cod_adm) AS id FROM hcneuro_admision", $adapter::QUERY_MODE_EXECUTE);

		# El código de admision se aumenta con programación (Propuesta de Oscar Galvis)
		foreach ($result as $key => $value) {
			$data['cod_adm'] = ($value->id) + 1;
			break;
		}
		$this->tableGateway->insert($data);

		return $data;
	}

	public function updateAdmission(Admission $adm)
	{

        $auth = new AuthenticationService();
        $user = $auth->getIdentity()->cod_usu;

		$data = array(
			'cod_tip_doc' => $adm->cod_tip_doc,
			'num_doc_pac' => $adm->num_doc_pac,
			'cod_usu_med' => $adm->cod_usu_med,
			'cod_are' => $adm->cod_are,
			'cod_ent' => $adm->cod_ent,
			'obs_adm' => $adm->obs_adm,
			'cod_usu_mod' => $user,
			'fec_mod' => date("Y-m-d H:i:s"),
		);

		$id = (int) $adm->cod_adm;
		$this->tableGateway->update($data, array('cod_adm' => $id));
	}

	public function deleteAdmission($id)
	{
		if ($this->isAdmission($id))
			$this->tableGateway->delete(array('cod_adm' => (int) $id));
		else
			throw new \Exception("La admisión no existe!");
	}

	public function openAdmission($id)
	{
		$admission = $this->getAdmission($id);

		if (count($this->getActivePatientAdmissions($admission->num_doc_pac, $admission->cod_tip_doc)))
			throw new \Exception("El paciente ya tiene una admisión abierta");

		$this->tableGateway->update(
			array('est_adm' => ""),
			array('cod_adm' => $id)
		);
	}

	public function annulAdmission($id)
	{
		$this->tableGateway->update(
			array('est_adm' => "A"),
			array('cod_adm' => $id)
		);
	}

	public function closeAdmission($id)
	{
		$this->tableGateway->update(
			array('est_adm' => "C"),
			array('cod_adm' => $id)
		);
	}

	public function countAdmissions()
	{
		$sql = "SELECT COUNT(cod_adm) AS total FROM hcneuro_admision";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function lastAdmissionRegistered()
	{
		$sql = "SELECT cod_adm, fec_adm, a.num_doc_pac, a.cod_tip_doc,
				CONCAT(pri_nom_pac, ' ', pri_ape_pac) AS nombre
				FROM hcneuro_admision AS a
				INNER JOIN hcneuro_pacientes AS b ON a.num_doc_pac = b.num_doc_pac AND a.cod_tip_doc = b.cod_tip_doc
				ORDER BY fec_adm DESC
				LIMIT 1";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current();
	}

}
