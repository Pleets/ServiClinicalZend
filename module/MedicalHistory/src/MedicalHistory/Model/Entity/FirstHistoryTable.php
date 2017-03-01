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

class FirstHistoryTable
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

	public function hasHistories()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_histo_primera')
		;
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$adapter = $this->dbAdapter;
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function search($pac = "", $options = null)
	{
		if (is_null($options)) $options = array();
		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;

		$sql = "SELECT b.cod_adm, c.est_adm, b.fecha_reg, a.cod_tip_doc, a.num_doc_pac,
				CONCAT(d.pri_nom_pac, ' ', d.seg_nom_pac, ' ', d.pri_ape_pac, ' ', d.seg_ape_pac) AS nom_pac, nom_tip_doc, a.num_fol, mot_con,
				rev_sis, enf_act, tas, tad, tam, fc, fr, tem, peso, talla, neu_men, cab_cue, tor_car,
				abd_dig, genito, ext_ost, ana_con
				FROM hcneuro_histo_primera AS a
				INNER JOIN hcneuro_hcingresos AS b ON a.cod_tip_doc = b.cod_tip_doc AND a.num_doc_pac = b.num_doc_pac AND a.num_fol = b.num_fol
				INNER JOIN hcneuro_admision AS c ON b.cod_adm = c.cod_adm
				INNER JOIN hcneuro_pacientes AS d ON a.cod_tip_doc = d.cod_tip_doc AND a.num_doc_pac = d.num_doc_pac
				INNER JOIN hcneuro_tipodoc AS e ON a.cod_tip_doc = e.cod_tip_doc
				WHERE b.cod_tip_his = 1
				ORDER BY b.fecha_reg DESC
				LIMIT $limit
				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset;
	}

	public function getHistory($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el folio $folio para el paciente $id");
		return $row;
	}

	public function isHistory($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addHistory(FirstHistory $history)
	{
		$data = array(
			'cod_tip_doc' => $history->cod_tip_doc,
			'num_doc_pac' => $history->num_doc_pac,
			'num_fol' => $history->num_fol,
			'mot_con' => $history->mot_con,
			'rev_sis' => $history->rev_sis,
			'enf_act' => $history->enf_act,
			'tas' => $history->tas,
			'tad' => $history->tad,
			'tam' => $history->tam,
			'fc' => $history->fc,
			'fr' => $history->fr,
			'tem' => $history->tem,
			'peso' => $history->peso,
			'talla' => $history->talla,
			'neu_men' => $history->neu_men,
			'cab_cue' => $history->cab_cue,
			'tor_car' => $history->tor_car,
			'abd_dig' => $history->abd_dig,
			'genito' => $history->genito,
			'ext_ost' => $history->ext_ost,
			'ana_con' => $history->ana_con,
		);

		$patient = $data["num_doc_pac"];
		$patient = $data["num_fol"];

		if ($this->isHistory($history->num_doc_pac, $history->cod_tip_doc, $history->num_fol))
			throw new \Exception("Ya existe una historia clínica para el paciente $patient con número de folio $folio");
		else {
			try {
			$result = $this->tableGateway->insert($data);
			} catch (\Exception $e) {
				var_dump($e);
			}
		}
	}

	public function updateHistory(FirstHistory $history)
	{
		$data = array(
			'mot_con' => $history->mot_con,
			'rev_sis' => $history->rev_sis,
			'enf_act' => $history->enf_act,
			'tas' => $history->tas,
			'tad' => $history->tad,
			'tam' => $history->tam,
			'fc' => $history->fc,
			'fr' => $history->fr,
			'tem' => $history->tem,
			'peso' => $history->peso,
			'talla' => $history->talla,
			'neu_men' => $history->neu_men,
			'cab_cue' => $history->cab_cue,
			'tor_car' => $history->tor_car,
			'abd_dig' => $history->abd_dig,
			'genito' => $history->genito,
			'ext_ost' => $history->ext_ost,
			'ana_con' => $history->ana_con,
		);

		$this->tableGateway->update($data, array('num_doc_pac' => $history->num_doc_pac, 'cod_tip_doc' => $history->cod_tip_doc, 'num_fol' => $history->num_fol));
	}

	public function deleteHistory($id, $type, $folio)
	{
		if ($this->isHistory($id, $type, $folio))
			$this->tableGateway->delete(array('num_doc_pac' => (string) $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		else
			throw new \Exception("El número de folio no existe");
	}
}
