<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Model\Entity;



use Zend\Db\Sql\Where;

use Zend\Db\Sql\Sql;



class HistoryTable

{

	private $sql;

	private $dbAdapter;



	public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter)

	{

		$this->sql = new Sql($dbAdapter);

		$this->dbAdapter = $dbAdapter;

	}



	public function getFoliosForPatient($num_doc_pac, $cod_tip_doc, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT a.cod_adm as admission, a.num_doc_pac AS documento, a.cod_tip_doc AS tipodoc,

				CONCAT(c.pri_nom_pac, ' ', c.seg_nom_pac, ' ', c.pri_ape_pac, ' ', c.seg_ape_pac) AS paciente, b.num_fol AS folio,

				fecha_reg AS registro, b.cod_tip_his AS tipohis, b.cod_usu AS usumed, nom_usu AS medico

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_pacientes AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc

				INNER JOIN hcneuro_usuarios AS d ON b.cod_usu = d.cod_usu

				WHERE a.num_doc_pac = $num_doc_pac AND a.cod_tip_doc = $cod_tip_doc

				ORDER BY b.num_fol DESC

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getFolioByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT a.cod_adm as admission, a.num_doc_pac AS documento, a.cod_tip_doc AS tipodoc,

				CONCAT(c.pri_nom_pac, ' ', c.seg_nom_pac, ' ', c.pri_ape_pac, ' ', c.seg_ape_pac) AS paciente, b.num_fol AS folio,

				fecha_reg AS registro, b.cod_tip_his AS tipohis, b.cod_usu AS usumed, nom_usu AS medico

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_pacientes AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc

				INNER JOIN hcneuro_usuarios AS d ON b.cod_usu = d.cod_usu

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		$row = $rowset->current();

		return $row;

	}



	public function getDiagnosticsByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT e.cod_dia, nom_dia, dia_pri, tip_dia, clasi_dia, clase_dia, dia_ing, dia_egr, obs_dia

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_hcdiapac AS e ON a.num_doc_pac = e.num_doc_pac AND a.cod_tip_doc = e.cod_tip_doc AND b.num_fol = e.num_fol

				INNER JOIN hcneuro_cie10 AS f ON e.cod_dia = f.cod_dia

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getMedicationsByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT e.cod_med, nom_med, can_med, num_dia, ter_med, cod_apl_med, pos_med

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_hcmedfor AS e ON a.num_doc_pac = e.num_doc_pac AND a.cod_tip_doc = e.cod_tip_doc AND b.num_fol = e.num_fol

				INNER JOIN hcneuro_medicamentos AS f ON e.cod_med = f.cod_med

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getExamsByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT e.cod_exa, nom_exa, can_exa, e.est_exa, obs_exa

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_hcexapac AS e ON a.num_doc_pac = e.num_doc_pac AND a.cod_tip_doc = e.cod_tip_doc AND b.num_fol = e.num_fol

				INNER JOIN hcneuro_examenes AS f ON e.cod_exa = f.cod_exa

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getInterconsultationsByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT cod_int, e.cod_dia, nom_dia, e.cod_esp, nom_esp, mot_int

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_interconsultas AS e ON a.num_doc_pac = e.num_doc_pac AND a.cod_tip_doc = e.cod_tip_doc AND b.num_fol = e.num_fol

				INNER JOIN hcneuro_cie10 AS c ON e.cod_dia = c.cod_dia

				INNER JOIN hcneuro_especialidades AS d ON e.cod_esp = d.cod_esp

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getIncapacitiesByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT e.cod_dia, nom_dia, fec_ini_inc, num_dia_inc, des_inc

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_incapacidad AS e ON a.num_doc_pac = e.num_doc_pac AND a.cod_tip_doc = e.cod_tip_doc AND b.num_fol = e.num_fol

				INNER JOIN hcneuro_cie10 AS c ON e.cod_dia = c.cod_dia

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getCertificationsByAdmission($admission, $options = null)

	{

		if (is_null($options)) $options = array();

		$options["limit"] = $limit = (array_key_exists("limit", $options)) ? $options["limit"] : 30;



		$sql = "SELECT cod_cer, tit_cer, des_cer, fec_reg

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_certificacion AS c ON a.cod_adm = c.cod_adm

				WHERE a.cod_adm = $admission

				LIMIT $limit

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getObservationsByAdmission($admission, $options = null)

	{

		$sql = "SELECT a.cod_tip_doc, a.num_doc_pac, b.num_fol, f.obs_med

				FROM hcneuro_admision AS a

				INNER JOIN hcneuro_hcingresos AS b ON a.cod_adm = b.cod_adm

				INNER JOIN hcneuro_histo_obser AS f ON b.cod_tip_doc = f.cod_tip_doc AND b.num_doc_pac = f.num_doc_pac AND b.num_fol = f.num_fol

				WHERE a.cod_adm = $admission

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset->current();

	}



	public function getBackgroundsByPatient($num_doc_pac, $cod_tip_doc, $options = null)

	{

		$sql = "SELECT cod_ant, b.num_doc_pac, b.cod_tip_doc, fec_reg, tip_ant, des_ant

				FROM hcneuro_antecedentes AS a

				INNER JOIN hcneuro_pacientes AS b ON b.cod_tip_doc = a.cod_tip_doc AND b.num_doc_pac = a.num_doc_pac

				WHERE b.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc'

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}



	public function getHistory($num_doc_pac, $cod_tip_doc, $num_fol, $cod_tip_his, $options = null)

	{

		$sql = "SELECT * FROM hcneuro_hcingresos AS a

				WHERE num_doc_pac = '$num_doc_pac' AND cod_tip_doc = '$cod_tip_doc' AND num_fol = '$num_fol'

				LIMIT 1

				";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);



		switch ($rowset->current()->cod_tip_his)

		{

			case '1':

				$sql = "SELECT * FROM hcneuro_hcingresos AS a

						LEFT JOIN hcneuro_histo_primera AS b ON a.num_doc_pac = b.num_doc_pac AND a.cod_tip_doc = b.cod_tip_doc AND a.num_fol = b.num_fol

						WHERE a.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc' AND a.num_fol = '$num_fol' and a.cod_tip_his = '$cod_tip_his'

						LIMIT 1

						";

				break;



			case '2':

				$sql = "SELECT * FROM hcneuro_hcingresos AS a

						LEFT JOIN hcneuro_histo_control AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc AND a.num_fol = c.num_fol

						WHERE a.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc' AND a.num_fol = '$num_fol' and a.cod_tip_his = '$cod_tip_his'

						LIMIT 1

						";

				break;



			case '4':

				$sql = "SELECT * FROM hcneuro_hcingresos AS a

						LEFT JOIN hcneuro_patologia AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc AND a.num_fol = c.num_fol

						WHERE a.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc' AND a.num_fol = '$num_fol' and a.cod_tip_his = '$cod_tip_his'

						LIMIT 1

						";

				break;



			case '5':

				$sql = "SELECT * FROM hcneuro_hcingresos AS a

						LEFT JOIN hcneuro_liquidos AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc AND a.num_fol = c.num_fol

						WHERE a.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc' AND a.num_fol = '$num_fol' and a.cod_tip_his = '$cod_tip_his'

						LIMIT 1

						";

				break;

			case '6':



				$sql = "SELECT * FROM hcneuro_hcingresos AS a

						LEFT JOIN hcneuro_citologia AS c ON a.num_doc_pac = c.num_doc_pac AND a.cod_tip_doc = c.cod_tip_doc AND a.num_fol = c.num_fol

						WHERE a.num_doc_pac = '$num_doc_pac' AND a.cod_tip_doc = '$cod_tip_doc' AND a.num_fol = '$num_fol' and a.cod_tip_his = '$cod_tip_his'

						LIMIT 1

						";

				break;

		}



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset->current();

	}

	public function getDatosIngreso($num_ing, $tipo_historia)
	{

		$sql="select  a.cod_tip_doc as tipo_documento,  a.num_doc_pac as documento, a.pri_nom_pac as primer_nombre, a.seg_nom_pac as segundo_nombre ,a.pri_ape_pac as primer_apellido, a.seg_ape_pac as segundo_apellido,
		c.numero as numero_examen,
		c.num_fol as numero_folio,
		c.cod_tip_his as tipo_historia,
		b.cod_adm as numero_admision
		from hcneuro_pacientes a inner join
		hcneuro_admision b on a.cod_tip_doc=b.cod_tip_doc and a.num_doc_pac=b.num_doc_pac
		inner join hcneuro_hcingresos c on c.cod_tip_doc=b.cod_tip_doc and c.num_doc_pac=b.num_doc_pac
		and c.cod_adm=b.cod_adm
		where b.cod_adm='$num_ing' and c.cod_tip_his=$tipo_historia ";



		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}

	public function getDatosRia($tipo_documento, $numero_documento, $numero_admision, $numero_folio)
	{

		$sql="select * from hcneuro_servicios_salud
		where
		cod_tip_doc=$tipo_documento and
		num_doc_pac='$numero_documento' and
		num_adm=$numero_admision and
		num_fol=$numero_folio ";
		//echo $sql;
		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		$row = $rowset->current();

		return $row;

		//return $rowset;

	}

	public function getDatosCupsRia($id_ria)
	{
		$sql="select * from hcneuro_servicios_salud_cups where id=$id_ria";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;

	}

	public function eliminarCupSia($id, $cod_cup)
	{

		$sql="delete from hcneuro_servicios_salud_cups
		where id=$id and  cod_cup='$cod_cup'";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
	}




	public function getCie10($valor_busqueda)
	{

		$sql="select cod_dia, nom_dia from hcneuro_cie10
		where nom_dia like '%".$valor_busqueda."%'
		or cod_dia='".$valor_busqueda."' limit 100
		";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;
	}

	public function guardarRia($valores, $id_ria)
	{

		$fecha_procedimiento=$valores['fecha_procedimiento'];
		$datos_fecha=explode("/", $fecha_procedimiento);

		$fecha_procedimiento=$datos_fecha[2]."-".$datos_fecha[1]."-".$datos_fecha[0];



		if(isset($valores['fur']))
		{

			$fecha_fur=$valores['fur'];
			$datos_fecha=explode("/", $fecha_fur);

			$fecha_fur=$datos_fecha[2]."-".$datos_fecha[1]."-".$datos_fecha[0];


		}

		$sql="INSERT INTO  hcneuro_servicios_salud
		VALUES
		(
		 ".$id_ria.",
		 ".$valores['tipo_documento'].",
		 '".$valores['documento']."',
		 ".$valores['numero_admision'].",
		 ".$valores['tipo_historia'].",
		 ".$valores['folio'].",
		 ".$valores['numero_examen'].",
		 '".$valores['paciente']."',
		 '".$valores['nro_autorizacion']."',
		 '".$fecha_procedimiento."',
		 '".$valores['persona_atiende']."',
		 '".$valores['clase_procedimiento']."',
		 '".$valores['condicion_usuaria']."',
		 ".$valores['cantidad_procedimiento'].",
		 '".$valores['tipo_procedimiento']."',
		 '".$valores['medico_remitente']."',
		 '".$valores['codigo_diagnostico']."',
		 '".$valores['cod_orden']."',
		 ".(isset($valores['fur']) ? "'".$fecha_fur."'" : "NULL" ).",
		 '".(isset($valores['gravidez']) ? $valores['gravidez'] : "" )."'
		)
		";
		//echo $sql;
		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

	}


	public function actualizarRia($valores, $id_ria)
	{

		$fecha_procedimiento=$valores['fecha_procedimiento'];
		$datos_fecha=explode("/", $fecha_procedimiento);

		$fecha_procedimiento=$datos_fecha[2]."-".$datos_fecha[1]."-".$datos_fecha[0];


		if(isset($valores['fur']))
		{

			$fecha_fur=$valores['fur'];
			$datos_fecha=explode("/", $fecha_fur);

			$fecha_fur=$datos_fecha[2]."-".$datos_fecha[1]."-".$datos_fecha[0];

		}


		$sql="update hcneuro_servicios_salud set

		 num_aut='".$valores['nro_autorizacion']."',
		 fec_pro='".$fecha_procedimiento."',
		 per_ati='".$valores['persona_atiende']."',
		 cla_pro='".$valores['clase_procedimiento']."',
		 con_usu='".$valores['condicion_usuaria']."',
		 can_pro=".$valores['cantidad_procedimiento'].",
		 tip_pro='".$valores['tipo_procedimiento']."',
		 med_rem='".$valores['medico_remitente']."',
		 cod_dia='".$valores['codigo_diagnostico']."',
		 cod_ord='".$valores['cod_orden']."',
		 fec_fur=".(isset($valores['fur']) ? "'".$fecha_fur."'" : "NULL" ).",
		 gravidez='".(isset($valores['gravidez']) ? $valores['gravidez'] : "" )."'
		 where id=$id_ria
		";
		//echo $sql;

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
	}

	public function guardarCupRia($id_ria, $cup, $valor_total, $copago, $valor)
	{

		$sql="insert into
		hcneuro_servicios_salud_cups
		values
		(
			$id_ria,
			'$cup',
			$valor_total,
			$copago,
			$valor
		)";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
	}


	public function getCups($valor_busqueda)
	{

		$sql="select cod_cup, nom_cup from hcneuro_cups
		where nom_cup like '%".$valor_busqueda."%'
		or cod_cup='".$valor_busqueda."' limit 150
		";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;
	}

	public function existeCups($id_ria, $cod_cup)
	{

		$sql="select * from hcneuro_servicios_salud_cups
		where id=$id_ria and cod_cup='$cod_cup'";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return count($rowset);
	}

	public function getMaximoIdRia()
	{
		$sql="SELECT ifnull( max(id) ,0)+1 as maximo from hcneuro_servicios_salud";

		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;
	}

	public function getDatosHistoriaPatoLiq($cod_tip_doc, $num_doc_pac, $num_fol)
	{

		$sql="select material_estudio, diagnostico_clinico from
		hcneuro_patologia where cod_tip_doc=$cod_tip_doc
		and num_doc_pac='$num_doc_pac' and num_fol=$num_fol ";
		$adapter = $this->dbAdapter;

		$rowset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

		return $rowset;
	}


}

