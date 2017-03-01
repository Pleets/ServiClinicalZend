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

class CitologiaTable
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

	public function hasCitologia()
	{
		return count($this->search("", array("limit" => 1)));
	}

	public function fetchAll()
	{
		$select = $this->sql->select();
		$sql = $select
				->from('hcneuro_citologia')
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
				CONCAT(d.pri_nom_pac, ' ', d.seg_nom_pac, ' ', d.pri_ape_pac, ' ', d.seg_ape_pac) AS nom_pac, nom_tip_doc, a.num_fol,
				cal_mues_a, cal_mues_b, cal_mues_c, cal_mues_d, cat_gen_a, cat_gen_b, mic_a, mic_b, mic_c, mic_d, mic_e,
				mic_f, mic_g, otr_haz_a, otr_haz_b, otr_haz_c, otr_haz_d, otr_haz_e, otr_haz_f, ano_cel_esc_a, ano_cel_esc_b,
				ano_cel_esc_c, ano_cel_esc_d, ano_cel_esc_e, ano_cel_esc_f, ano_cel_gla_a, ano_cel_gla_b, ano_cel_gla_c,
				ano_cel_gla_d, ano_cel_gla_e, ano_cel_gla_f, ano_cel_gla_g, ano_cel_gla_h, observaciones, f.id id_salud
				FROM hcneuro_citologia AS a
				INNER JOIN hcneuro_hcingresos AS b ON a.cod_tip_doc = b.cod_tip_doc AND a.num_doc_pac = b.num_doc_pac AND a.num_fol = b.num_fol
				INNER JOIN hcneuro_admision AS c ON b.cod_adm = c.cod_adm
				INNER JOIN hcneuro_pacientes AS d ON a.cod_tip_doc = d.cod_tip_doc AND a.num_doc_pac = d.num_doc_pac
				INNER JOIN hcneuro_tipodoc AS e ON a.cod_tip_doc = e.cod_tip_doc
				LEFT JOIN hcneuro_servicios_salud AS f on a.cod_tip_doc = f.cod_tip_doc AND a.num_doc_pac = f.num_doc_pac AND a.num_fol = f.num_fol and f.cod_tip_his = 6
															and f.num_adm = b.cod_adm
				WHERE b.cod_tip_his = 6
				ORDER BY b.fecha_reg DESC
				LIMIT $limit
				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset;
	}

	public function getCitologia($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			throw new \Exception("No se pudo encontrar el folio $folio para el paciente $id");
		return $row;
	}

	public function isCitologia($id, $type, $folio)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		$row = $rowset->current();
		if (!$row)
			return false;
		return true;
	}

	public function addCitologia(Citologia $history)
	{
		$data = array(
			'cod_tip_doc' => $history->cod_tip_doc,
			'num_doc_pac' => $history->num_doc_pac,
			'num_fol' => $history->num_fol,
			'cal_mues_a' =>$history->cal_mues_a,
			'cal_mues_b' =>$history->cal_mues_b,
			'cal_mues_c' =>$history->cal_mues_c,
			'cal_mues_d' =>$history->cal_mues_d,
			'cat_gen_a' =>$history->cat_gen_a,
			'cat_gen_b' =>$history->cat_gen_b,
			'mic_a' =>$history->mic_a,
			'mic_b' =>$history->mic_b,
			'mic_c' =>$history->mic_c,
			'mic_d' =>$history->mic_d,
			'mic_e' =>$history->mic_e,
			'mic_f' =>$history->mic_f,
			'mic_g' =>$history->mic_g,
			'otr_haz_a' =>$history->otr_haz_a,
			'otr_haz_b' =>$history->otr_haz_b,
			'otr_haz_c' =>$history->otr_haz_c,
			'otr_haz_d' =>$history->otr_haz_d,
			'otr_haz_e' =>$history->otr_haz_e,
			'otr_haz_f' =>$history->otr_haz_f,
			'ano_cel_esc_a' =>$history->ano_cel_esc_a,
			'ano_cel_esc_b' =>$history->ano_cel_esc_b,
			'ano_cel_esc_c' =>$history->ano_cel_esc_c,
			'ano_cel_esc_d' =>$history->ano_cel_esc_d,
			'ano_cel_esc_e' =>$history->ano_cel_esc_e,
			'ano_cel_esc_f' =>$history->ano_cel_esc_f,
			'ano_cel_gla_a' =>$history->ano_cel_gla_a,
			'ano_cel_gla_b' =>$history->ano_cel_gla_b,
			'ano_cel_gla_c' =>$history->ano_cel_gla_c,
			'ano_cel_gla_d' =>$history->ano_cel_gla_d,
			'ano_cel_gla_e' =>$history->ano_cel_gla_e,
			'ano_cel_gla_f' =>$history->ano_cel_gla_f,
			'ano_cel_gla_g' =>$history->ano_cel_gla_g,
			'ano_cel_gla_h' =>$history->ano_cel_gla_h,
			'observaciones' =>$history->observaciones,

		);

		$patient = $data["num_doc_pac"];
		$patient = $data["num_fol"];

		if ($this->isCitologia($history->num_doc_pac, $history->cod_tip_doc, $history->num_fol))
			throw new \Exception("Ya existe un historia clinica de Citologia para el paciente $patient con número de folio $folio");
		else {
			try {
			$result = $this->tableGateway->insert($data);
			} catch (\Exception $e) {
				var_dump($e);
			}
		}
	}

	public function updateCitologia(Citologia $history)
	{
		$data = array(
			'cal_mues_a' =>$history->cal_mues_a,
			'cal_mues_b' =>$history->cal_mues_b,
			'cal_mues_c' =>$history->cal_mues_c,
			'cal_mues_d' =>$history->cal_mues_d,
			'cat_gen_a' =>$history->cat_gen_a,
			'cat_gen_b' =>$history->cat_gen_b,
			'mic_a' =>$history->mic_a,
			'mic_b' =>$history->mic_b,
			'mic_c' =>$history->mic_c,
			'mic_d' =>$history->mic_d,
			'mic_e' =>$history->mic_e,
			'mic_f' =>$history->mic_f,
			'mic_g' =>$history->mic_g,
			'otr_haz_a' =>$history->otr_haz_a,
			'otr_haz_b' =>$history->otr_haz_b,
			'otr_haz_c' =>$history->otr_haz_c,
			'otr_haz_d' =>$history->otr_haz_d,
			'otr_haz_e' =>$history->otr_haz_e,
			'otr_haz_f' =>$history->otr_haz_f,
			'ano_cel_esc_a' =>$history->ano_cel_esc_a,
			'ano_cel_esc_b' =>$history->ano_cel_esc_b,
			'ano_cel_esc_c' =>$history->ano_cel_esc_c,
			'ano_cel_esc_d' =>$history->ano_cel_esc_d,
			'ano_cel_esc_e' =>$history->ano_cel_esc_e,
			'ano_cel_esc_f' =>$history->ano_cel_esc_f,
			'ano_cel_gla_a' =>$history->ano_cel_gla_a,
			'ano_cel_gla_b' =>$history->ano_cel_gla_b,
			'ano_cel_gla_c' =>$history->ano_cel_gla_c,
			'ano_cel_gla_d' =>$history->ano_cel_gla_d,
			'ano_cel_gla_e' =>$history->ano_cel_gla_e,
			'ano_cel_gla_f' =>$history->ano_cel_gla_f,
			'ano_cel_gla_g' =>$history->ano_cel_gla_g,
			'ano_cel_gla_h' =>$history->ano_cel_gla_h,
			'observaciones' =>$history->observaciones,
		);

		$this->tableGateway->update($data, array('num_doc_pac' => $history->num_doc_pac, 'cod_tip_doc' => $history->cod_tip_doc, 'num_fol' => $history->num_fol));
	}

	public function deleteCitologia($id, $type, $folio)
	{
		if ($this->isCitologia($id, $type, $folio))
			$this->tableGateway->delete(array('num_doc_pac' => (string) $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		else
			throw new \Exception("La citología no existe");
	}
}
