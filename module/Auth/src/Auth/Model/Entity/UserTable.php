<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class UserTable
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

	public function hasUsers()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_usuarios')
				->join('hcneuro_perfil', 'hcneuro_usuarios.cod_per = hcneuro_perfil.cod_per',
					array('nom_per'),
					$select::JOIN_LEFT)
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function countUsers()
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM hcneuro_usuarios";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countActiveUsers()
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM hcneuro_usuarios
				WHERE est_usu = 1";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countInactiveUsers()
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM hcneuro_usuarios
				WHERE est_usu = 2";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function lastUserRegistered()
	{
		$sql = "SELECT * FROM hcneuro_usuarios
				ORDER BY fec_reg_usu DESC
				LIMIT 1";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current();
	}

	public function fetchDoctors()
	{
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_usuarios')
				->join('hcneuro_perfil', 'hcneuro_usuarios.cod_per = hcneuro_perfil.cod_per',
					array('nom_per'),
					$select::JOIN_INNER)
		;
		$join->where(array("hcneuro_perfil.cod_per" => 4));
		$sql = $join->order('cod_usu ASC');

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($user = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($user) {
		    $where->like('nom_usu', "%$user%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_usuarios')
				->join('hcneuro_perfil', 'hcneuro_usuarios.cod_per = hcneuro_perfil.cod_per',
					array('nom_per'),
					$select::JOIN_LEFT)
		;
		$join->where($spec);
		$sql = $join->order('cod_usu ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getUser($id)
	{
		$sql = "SELECT cod_usu, nom_usu, a.cod_per, nom_per, fec_reg_usu, est_usu, pas_usu, fir_usu
				FROM hcneuro_usuarios AS a
				INNER JOIN hcneuro_perfil AS b ON a.cod_per = b.cod_per
				WHERE a.cod_usu = '$id'
				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current();
	}

	public function getPermission($id)
	{
		return $this->getUser($id)->cod_per;
	}

	public function isUser($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_usu' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isName($nom_usu)
	{
		$rowset = $this->tableGateway->select(array('nom_usu' => $nom_usu));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addUser(User $user)
	{
		$data = array(
			'cod_usu' => $user->cod_usu,
			'nom_usu' => $user->nom_usu,
			'cod_per' => $user->cod_per,
			'est_usu' => $user->est_usu,
			'pas_usu' => $user->pas_usu,
			'fec_reg_usu' => date('Y-m-d H:i:s'),
		);

		if ($user->cod_per == 0)
			unset($data["cod_per"]);

		if ($this->isUser($user->cod_usu))
			throw new \Exception("Ya existe un usuario con este documento");
		if ($this->isName($user->nom_usu))
			throw new \Exception("Este nombre de usuario ya existe!");
		else
			$this->tableGateway->insert($data);
	}

	public function updateUser(User $user)
	{
		$data = array(
			'nom_usu' => $user->nom_usu,
			'cod_per' => $user->cod_per,
		);

		if ($user->cod_per == 0)
			$data["cod_per"] = null;

		$id = (string) $user->cod_usu;
		if ($thisUser = $this->getUser($id)) {
			if (!$this->isName($user->nom_usu) || strtolower($thisUser->nom_usu) == strtolower($user->nom_usu)) {
				$this->tableGateway->update($data, array('cod_usu' => $id));
			}
			else
				throw new \Exception("Este nombre de usuario ya existe!");
		}
		else
			throw new \Exception("El usuario $id no existe");
	}

	public function changePassword(User $user)
	{
		$data = array(
			'pas_usu' => $user->pas_usu,
		);

		$id = (string) $user->cod_usu;
		if ($thisUser = $this->getUser($id))
				$this->tableGateway->update($data, array('cod_usu' => $id));
		else
			throw new \Exception("El usuario $id no existe");
	}

	public function deleteUser($id)
	{
		if ($this->isUser($id))
			$this->tableGateway->delete(array('cod_usu' => (string) $id));
		else
			throw new \Exception("El usuario $id no existe!");
	}

	public function disableUser($id)
	{
		$this->tableGateway->update(
			array('est_usu' => 2),
			array('cod_usu' => $id)
		);
	}

	public function enableUser($id)
	{
		$this->tableGateway->update(
			array('est_usu' => 1),
			array('cod_usu' => $id)
		);
	}

	public function countAdmissions($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu_reg) AS total FROM hcneuro_admision
				WHERE cod_usu_reg = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countPatients($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu_reg) AS total FROM hcneuro_pacientes
				WHERE cod_usu_reg = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countIncome($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM hcneuro_hcingresos
				WHERE cod_usu = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countEntries($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM log_ingresos
				WHERE cod_usu = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

	public function countCertifications($cod_usu)
	{
		$sql = "SELECT COUNT(cod_usu) AS total FROM hcneuro_certificacion
				WHERE cod_usu = '$cod_usu'";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current()->total;
	}

}
