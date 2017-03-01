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

class AdjuntosTable
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

	public function search($pac = "", $options = null)
	{
		if (is_null($options)) $options = array();
		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;

		$sql = "SELECT b.cod_adm, c.est_adm, b.fecha_reg, a.cod_tip_doc, a.num_doc_pac,
				CONCAT(d.pri_nom_pac, ' ', d.seg_nom_pac, ' ', d.pri_ape_pac, ' ', d.seg_ape_pac) AS nom_pac, nom_tip_doc, a.num_fol,
				material_estudio, diagnostico_clinico, descripcion_macroscopica, descripcion_microscopica, diagnostico, nota
				FROM hcneuro_liquidos AS a
				INNER JOIN hcneuro_hcingresos AS b ON a.cod_tip_doc = b.cod_tip_doc AND a.num_doc_pac = b.num_doc_pac AND a.num_fol = b.num_fol
				INNER JOIN hcneuro_admision AS c ON b.cod_adm = c.cod_adm
				INNER JOIN hcneuro_pacientes AS d ON a.cod_tip_doc = d.cod_tip_doc AND a.num_doc_pac = d.num_doc_pac
				INNER JOIN hcneuro_tipodoc AS e ON a.cod_tip_doc = e.cod_tip_doc
				WHERE b.cod_tip_his = 5
				ORDER BY b.fecha_reg DESC
				LIMIT $limit
				";

		$adapter = $this->dbAdapter;
		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
		return $rowset;
	}

	public function getAdjuntos($id, $type, $folio, $history_type)
	{
		$id = (string) $id;
		$rowset = $this->tableGateway->select(array('num_doc_pac' => $id, 'cod_tip_doc' => $type, 'num_fol' => $folio, 'cod_tip_his' => $history_type));

		return $rowset;
	}

	public function addAdjuntos(Adjuntos $ajunto)
	{
		$data = array(
			'cod_tip_doc'  => $ajunto->cod_tip_doc,
			'num_doc_pac'  => $ajunto->num_doc_pac,
			'num_fol'      => $ajunto->num_fol,
			'cod_adm'      => $ajunto->cod_adm,
			'cod_tip_his'  => $ajunto->cod_tip_his,
			'tipo_archivo' => $ajunto->tipo_archivo,
			'url_archivo'  => $ajunto->url_archivo
		);

		return $this->tableGateway->insert($data);
	}

	public function updateLiquidos(Liquidos $history)
	{
		$data = array(
			'material_estudio' => $history->material_estudio,
            'diagnostico_clinico' => $history->diagnostico_clinico,
            'descripcion_macroscopica' => $history->descripcion_macroscopica,
            'descripcion_microscopica' => $history->descripcion_microscopica,
            'diagnostico' => $history->diagnostico,
            'nota' => $history->nota,
		);

		$this->tableGateway->update($data, array('num_doc_pac' => $history->num_doc_pac, 'cod_tip_doc' => $history->cod_tip_doc, 'num_fol' => $history->num_fol));
	}

	public function deleteLiquidos($id, $type, $folio)
	{
		if ($this->isLiquidos($id, $type, $folio))
			$this->tableGateway->delete(array('num_doc_pac' => (string) $id, 'cod_tip_doc' => $type, 'num_fol' => $folio));
		else
			throw new \Exception("La observación no existe");
	}
}
