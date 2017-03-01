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

class ProfileTable
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

	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	public function search($profile)
	{
		$spec = function (Where $where) use ($profile) {
		    $where->like('nom_per', "%$profile%");
		};
		$sql = $this->sql->select()->from('hcneuro_perfil')
				->where($spec)
				->order('nom_per ASC')
				->limit(100)
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getProfile($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_per' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isProfile($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('cod_per' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isProfileName($nom_per)
	{
		$rowset = $this->tableGateway->select(array('nom_per' => $nom_per));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addProfile(User $user)
	{
		$data = array(
			'cod_usu' => $user->cod_per,
			'nom_usu' => $user->nom_per,
			'cod_per' => $user->est_per,
		);

		if ($this->isProfile($user->cod_per))
			throw new \Exception("Ya existe un perfile con este identificador!");
		if ($this->isProfileName($user->nom_per))
			throw new \Exception("Este nombre de perfile ya existe!");
		else
			$this->tableGateway->insert($data);
	}

	public function updateProfile(Profile $profile)
	{
		$data = array(
			'nom_per' => $user->nom_per,
		);

		$id = (int) $user->cod_per;
		if ($thisUser = $this->getProfile($id)) {
			if (!$this->isProfileName($user->nom_per) || strtolower($thisUser->nom_per) == strtolower($user->nom_per))
				$this->tableGateway->update($data, array('cod_per' => $id));
			else
				throw new \Exception("Este nombre de perfil ya existe!");
		}
	}

	public function deleteProfile($id)
	{
		if ($this->isProfile($id))
			$this->tableGateway->delete(array('cod_per' => (int) $id));
		else
			throw new \Exception("El perfil no existe!");
	}

	public function disableProfile($id)
	{
		$this->tableGateway->update(
			array('est_per' => 2),
			array('cod_per' => $id)
		);
	}

	public function enableProfile($id)
	{
		$this->tableGateway->update(
			array('est_per' => 1),
			array('cod_per' => $id)
		);
	}

}
