<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Model\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PatientDiagnostic implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $num_fol;

	public $cod_dia;
	public $tip_dia;
	public $clasi_dia;
	public $clase_dia;
	public $dia_ing;
	public $dia_egr;
	public $obs_dia;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->num_fol = (isset($data["num_fol"])) ? $data["num_fol"] : null;

		$this->cod_dia = (isset($data["cod_dia"])) ? $data["cod_dia"] : null;
		$this->dia_pri = (isset($data["dia_pri"])) ? $data["dia_pri"] : null;
		$this->tip_dia = (isset($data["tip_dia"])) ? $data["tip_dia"] : null;
		$this->clasi_dia = (isset($data["clasi_dia"])) ? $data["clasi_dia"] : null;
		$this->clase_dia = (isset($data["clase_dia"])) ? $data["clase_dia"] : null;
		$this->dia_ing = (isset($data["dia_ing"])) ? $data["dia_ing"] : null;
		$this->dia_egr = (isset($data["dia_egr"])) ? $data["dia_egr"] : null;
		$this->obs_dia = (isset($data["obs_dia"])) ? $data["obs_dia"] : null;
	}

	public function exchangeJson($data = null)
	{
		$this->cod_tip_doc = (isset($data->cod_tip_doc)) ? $data->cod_tip_doc : null;
		$this->num_doc_pac = (isset($data->num_doc_pac)) ? $data->num_doc_pac : null;
		$this->num_fol = (isset($data->num_fol)) ? $data->num_fol : null;

		$this->cod_dia = (isset($data->cod_dia)) ? $data->cod_dia : null;
		$this->dia_pri = (isset($data->dia_pri)) ? $data->dia_pri : null;
		$this->tip_dia = (isset($data->tip_dia)) ? $data->tip_dia : null;
		$this->clasi_dia = (isset($data->clasi_dia)) ? $data->clasi_dia : null;
		$this->clase_dia = (isset($data->clase_dia)) ? $data->clase_dia : null;
		$this->dia_ing = (isset($data->dia_ing)) ? $data->dia_ing : null;
		$this->dia_egr = (isset($data->dia_egr)) ? $data->dia_egr : null;
		$this->obs_dia = (isset($data->obs_dia)) ? $data->obs_dia : null;
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}

	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	public function getInputFilter()
	{
		if (!$this->inputFilter)
		{
			$inputFilter = new InputFilter();

			$inputFilter->add(array(
				'name' => 'cod_dia',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => false)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 1, 'max' => 6)),
				),
			));

			$inputFilter->add(array(
				'name' => 'nom_dia',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 1000)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
