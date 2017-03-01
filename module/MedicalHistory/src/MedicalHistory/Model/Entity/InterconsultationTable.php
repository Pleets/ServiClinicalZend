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

class InterconsultationTable
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

	public function hasInterconsultations()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_interconsultas');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($interconsultation = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($interconsultation) {
		    $where->like('cod_int', "%$interconsultation%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_interconsultas')
		;
		$join->where($spec);
		$sql = $join->order('cod_int ASC')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getInterconsultation($num_doc_pac, $cod_tip_doc, $num_fol, $cod_int)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_int' => $cod_int));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la interconsulta con el código $cod_int");
		return $row;
	}

	public function getInterconsultationByCode($cod_int)
	{
		$rowset = $this->tableGateway->select(array('cod_int' => $cod_int));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar la interconsulta con el código $cod_int");
		return $row;
	}

	public function getFullInterconsultationByCode($cod_int)
	{
		$sql = "SELECT c.cod_int, a.cod_adm, b.cod_tip_doc, b.num_doc_pac, c.num_fol, c.cod_dia, nom_dia, c.cod_esp, nom_esp, mot_int
				FROM hcneuro_admision AS a
				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm
				INNER JOIN hcneuro_interconsultas AS c ON b.cod_tip_doc = c.cod_tip_doc AND b.num_doc_pac = c.num_doc_pac AND b.num_fol = c.num_fol
				INNER JOIN hcneuro_cie10 AS d ON c.cod_dia = d.cod_dia
				INNER JOIN hcneuro_especialidades AS e ON c.cod_esp = e.cod_esp
				WHERE c.cod_int = $cod_int

				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset->current();
	}

	public function getInterconsultationsByFolio($num_doc_pac, $cod_tip_doc, $num_fol)
	{
		$rowset = $this->tableGateway->select(array('cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol));
		return $rowset;
	}

	public function isPatientInterconsultation($cod_int, $num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia)
	{
		$rowset = $this->tableGateway->select(array('cod_int' => $cod_int, 'cod_tip_doc' => $cod_tip_doc, 'num_doc_pac' => $num_doc_pac, 'num_fol' => $num_fol, 'cod_dia' => $cod_dia));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function deleteInterconsultation($cod_int, $num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia)
	{
		if ($this->isPatientInterconsultation($cod_int, $num_doc_pac, $cod_tip_doc, $num_fol, $cod_dia))
			$this->tableGateway->delete(array('cod_int' => $cod_int, 'num_doc_pac' => $num_doc_pac, 'cod_tip_doc' => $cod_tip_doc, 'num_fol' => $num_fol));
		else
			throw new \Exception("La interconsulta no existe!");
	}

	public function addInterconsultation(Interconsultation $int)
	{
		$data = array(
			'cod_int' => $int->cod_int,
			'cod_tip_doc' => $int->cod_tip_doc,
			'num_doc_pac' => $int->num_doc_pac,
			'num_fol' => $int->num_fol,
			'cod_dia' => $int->cod_dia,
			'mot_int' => $int->mot_int,
			'cod_esp' => $int->cod_esp,
		);

		$interconsultation = $data["cod_int"];

		if ($this->isPatientInterconsultation($int->cod_int, $int->num_doc_pac, $int->cod_tip_doc, $int->num_fol, $int->cod_dia))
			throw new \Exception("Ya existe la interconsulta $interconsultation en la admisión");

		try {
			$this->tableGateway->insert($data);
		} catch (\Exception $e) {
			var_dump($e);
		}

		return $data;
	}

	public function updateInterconsultation(Interconsultation $int)
	{
		$data = array(
			'cod_dia' => $int->cod_dia,
			'mot_int' => $int->mot_int,
			'cod_esp' => $int->cod_esp,
		);

		$interconsultation = $int->cod_int;

		$type = $int->cod_tip_doc;
		$doc = $int->num_doc_pac;
		$folio = $int->num_fol;

		if (!$this->isPatientInterconsultation($int->cod_int, $int->num_doc_pac, $int->cod_tip_doc, $int->num_fol, $int->cod_dia))
			throw new \Exception("No existe la interconsulta $interconsultation en la admisión");

		$this->tableGateway->update($data, array('cod_tip_doc' => $type, 'num_doc_pac' => $doc, 'num_fol' => $folio, 'cod_int' => $interconsultation));

		return $data;
	}

}
