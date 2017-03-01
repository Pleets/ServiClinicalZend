<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class DocumentTable
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
		$select = $this->sql->select();
		$sql = $select ->from('hcneuro_tipodoc');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($doc = "")
	{
		$spec = function (Where $where) use ($doc) {
		    $where->like('nom_tip_doc', "%$doc%");
		};
		$select = $this->sql->select();
		$join = $select->from('hcneuro_tipodoc');
		$join->where($spec);
		$sql = $join->order('cod_tip_doc ASC')->limit(100);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getDocument($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $id));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el registro $id");
		return $row;
	}

	public function isPatient($id)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $id));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addPatient(Patient $doc)
	{
		$data = array(
			'cod_tip_doc' => $doc->cod_tip_doc,
			'nom_tip_doc' => $doc->nom_tip_doc,
			'est_tip_doc' => $doc->est_tip_doc,
		);

		if ($this->isDocument($doc->cod_tip_doc))
			throw new \Exception("Ya existe un documento con este código");
		else
			$this->tableGateway->insert($data);
	}

	public function updatePatient(Document $pac)
	{
		$data = array(
			'nom_tip_doc' => $doc->nom_tip_doc,
			'est_tip_doc' => $doc->est_tip_doc,
		);

		$id = (int) $doc->cod_tip_doc;
		$this->tableGateway->update($data, array('cod_tip_doc' => $id));
	}

	public function deleteDocument($id)
	{
		if ($this->isDocument($id))
			$this->tableGateway->delete(array('cod_tip_doc' => (int) $id));
		else
			throw new \Exception("El Documento no existe!");
	}
}
