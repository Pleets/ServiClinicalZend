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

class IngressTable
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

	public function hasIncome()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_hcingresos');
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($diagnostic = "", $options = null)
	{
		$options["limit"] = isset($options["limit"]) ? $options["limit"]: 30;

		$spec = function (Where $where) use ($diagnostic) {
		    $where->like('cod_adm', "%$diagnostic%");
		};
		$select = $this->sql->select();
		$join = $select
				->from('hcneuro_hcingresos')
		;
		$join->where($spec);
		$sql = $join->order('cod_adm')->limit($options["limit"]);

		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function getFullIngress($id)
	{
		$sql = "SELECT a.cod_adm, fec_adm, est_adm, h.num_fol, nom_tip_doc, a.num_doc_pac, a.cod_tip_doc,
				CONCAT(pri_nom_pac, ' ', seg_nom_pac, ' ', pri_ape_pac, ' ', seg_ape_pac) AS nom_pac, dir_pac, fec_nac_pac, num_tel_pac, nom_are, nom_ent,
				obs_adm, f.nom_usu AS usu_med, f.cod_usu AS cod_usu_med, e.nom_usu AS usu_reg, e.cod_usu AS cod_usu_reg
				FROM hcneuro_admision AS a
				INNER JOIN hcneuro_areas AS b ON a.cod_are = b.cod_are
				INNER JOIN hcneuro_entidad AS c ON a.cod_ent = c.cod_ent
				INNER JOIN hcneuro_tipodoc AS d ON a.cod_tip_doc = d.cod_tip_doc
				INNER JOIN hcneuro_usuarios AS e ON a.cod_usu_reg = e.cod_usu
				INNER JOIN hcneuro_usuarios AS f ON a.cod_usu_med = f.cod_usu
				INNER JOIN hcneuro_pacientes AS g ON a.num_doc_pac = g.num_doc_pac AND a.cod_tip_doc = g.cod_tip_doc
				INNER JOIN hcneuro_hcingresos AS h ON a.cod_adm = h.cod_adm
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

	public function getIngress($code)
	{
		$code = (int) $code;
		$rowset = $this->tableGateway->select(array('cod_adm' => $code));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el ingreso con código de admisión $code");
		return $row;
	}

	public function isIngress($code)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_adm' => $code));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function isIngressByHistoryType($code, $type)
	{
		$code = (string) $code;
		$rowset = $this->tableGateway->select(array('cod_adm' => $code, 'cod_tip_his' => $type));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function getFolio($code)
	{
		$code = (int) $code;
		$rowset = $this->tableGateway->select(array('cod_adm' => $code));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el ingreso con código de admisión $code");
		return $row->num_fol;
	}

	public function getLastFolio($doc, $type)
	{
		$adapter = $this->dbAdapter;
		$sql = "SELECT MAX(num_fol) AS folio FROM hcneuro_hcingresos WHERE num_doc_pac = '$doc' AND cod_tip_doc = '$type'";
		$result = $this->dbAdapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		foreach ($result as $key => $value) {
			if (is_null($value->folio))
				return 0;
			return $value->folio;
		}
	}

	public function addIngress(Ingress $ing)
	{
		$data = array(
			'cod_tip_doc' => $ing->cod_tip_doc,
			'num_doc_pac' => $ing->num_doc_pac,
			'num_fol' => $ing->num_fol,
			'cod_adm' => $ing->cod_adm,
			'cod_tip_his' => $ing->cod_tip_his,
			'fecha_reg' => $ing->fecha_reg,
			'cod_usu' => $ing->cod_usu
		);

		$adm = $ing->cod_adm;

		if ($this->isIngressByHistoryType($adm, $ing->cod_tip_his))
			throw new \Exception("Ya existe un ingreso con el número de admisión $adm");

		$this->tableGateway->insert($data);

		return $data;
	}
}
