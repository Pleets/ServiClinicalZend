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

class PatientsTable
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

	public function hasPatients()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_pacientes')
				->join('hcneuro_tipodoc', 'hcneuro_pacientes.cod_tip_doc = hcneuro_tipodoc.cod_tip_doc',
					array('nom_tip_doc'),
					$select::JOIN_INNER)
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($pac = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		/*$spec = function (Where $where) use ($pac) {
		    $where->like('num_doc_pac', "%$pac%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_pacientes')
				->join('hcneuro_tipodoc', 'hcneuro_pacientes.cod_tip_doc = hcneuro_tipodoc.cod_tip_doc',
					array('nom_tip_doc'),
					$select::JOIN_INNER)
		;
		$join->where($spec);
		$sql = $join->order('num_doc_pac ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;*/

		$sql = "SELECT * FROM hcneuro_pacientes AS a
				INNER JOIN hcneuro_tipodoc AS b ON a.cod_tip_doc = b.cod_tip_doc
				WHERE num_doc_pac LIKE '%$pac%' OR pri_nom_pac LIKE '%$pac%'
				OR seg_nom_pac LIKE '%$pac%' OR pri_ape_pac LIKE '%$pac%' OR seg_ape_pac LIKE '%$pac%'
				ORDER BY fec_reg_pac DESC
				LIMIT {$options["limit"]}
				";

		$adapter = $this->dbAdapter;
		$results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $results;

	}

	public function getPatient($id, $type)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isPatient($id, $type)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addPatient(Patient $pac)
	{
		$auth = new AuthenticationService();
		$cod_usu_reg = $auth->getIdentity()->cod_usu;

		$data = array(
			'cod_tip_doc' => $pac->cod_tip_doc,
			'num_doc_pac' => $pac->num_doc_pac,
			'pri_nom_pac' => $pac->pri_nom_pac,
			'seg_nom_pac' => $pac->seg_nom_pac,
			'pri_ape_pac' => $pac->pri_ape_pac,
			'seg_ape_pac' => $pac->seg_ape_pac,
			'fec_nac_pac' => $pac->fec_nac_pac,
			'dir_pac' => $pac->dir_pac,
			'num_tel_pac' => $pac->num_tel_pac,
			'num_cel_pac' => $pac->num_cel_pac,
			'cod_usu_reg' => $cod_usu_reg,
			'fec_reg_pac' => date("Y:m:d H:i:s"),
		);

		if ($this->isPatient($pac->num_doc_pac, $pac->cod_tip_doc))
			throw new \Exception("Ya existe un paciente con esta identificación");
		else {
			$result = $this->tableGateway->insert($data);
		}
	}

	public function updatePatient(Patient $pac)
	{
		$auth = new AuthenticationService();
		$cod_usu_mod = $auth->getIdentity()->cod_usu;

		$data = array(
			'cod_tip_doc' => $pac->cod_tip_doc,
			'pri_nom_pac' => $pac->pri_nom_pac,
			'seg_nom_pac' => $pac->seg_nom_pac,
			'pri_ape_pac' => $pac->pri_ape_pac,
			'seg_ape_pac' => $pac->seg_ape_pac,
			'fec_nac_pac' => $pac->fec_nac_pac,
			'dir_pac' => $pac->dir_pac,
			'num_tel_pac' => $pac->num_tel_pac,
			'num_cel_pac' => $pac->num_cel_pac,
			'cod_usu_mod' => $cod_usu_mod,
			'fec_mod_pac' => date("Y:m:d H:i:s"),
		);

		$id = (string) $pac->num_doc_pac;
		$this->tableGateway->update($data, array('num_doc_pac' => $id, 'cod_tip_doc' => $pac->cod_tip_doc));
	}

	public function deletePatient($id, $type)
	{
		if ($this->isPatient($id, $type))
			$this->tableGateway->delete(array('num_doc_pac' => (string) $id, 'cod_tip_doc' => $type));
		else
			throw new \Exception("El paciente no existe!");
	}

	public function countPatients()
	{
		$sql = "SELECT COUNT(num_doc_pac) AS total FROM hcneuro_pacientes";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function lastPatientRegistered()
	{
		$sql = "SELECT *
				FROM hcneuro_pacientes AS a
				ORDER BY fec_reg_pac DESC
				LIMIT 1";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current();
	}
}
