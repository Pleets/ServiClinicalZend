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

class Ingress implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $cod_adm;
	public $cod_tip_his;
	public $fecha_reg;
	public $cod_usu;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->num_fol = (isset($data["num_fol"])) ? $data["num_fol"] : null;
		$this->cod_adm = (isset($data["cod_adm"])) ? $data["cod_adm"] : null;
		$this->cod_tip_his = (isset($data["cod_tip_his"])) ? $data["cod_tip_his"] : null;
		$this->fecha_reg = (isset($data["fecha_reg"])) ? $data["fecha_reg"] : null;
		$this->cod_usu = (isset($data["cod_usu"])) ? $data["cod_usu"] : null;
	}

	public function exchangeJson($data = null)
	{
		$this->cod_tip_doc = (isset($data->cod_tip_doc)) ? $data->cod_tip_doc : null;
		$this->num_doc_pac = (isset($data->num_doc_pac)) ? $data->num_doc_pac : null;
		$this->num_fol = (isset($data->num_fol)) ? $data->num_fol : null;

		$this->cod_adm = (isset($data->cod_adm)) ? $data->cod_adm : null;
		$this->cod_tip_his = (isset($data->cod_tip_his)) ? $data->cod_tip_his : null;
		$this->fecha_reg = (isset($data->fecha_reg)) ? $data->fecha_reg : null;
		$this->cod_usu = (isset($data->cod_usu)) ? $data->cod_usu : null;
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
				'name' => 'cod_tip_doc',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

			$inputFilter->add(array(
				'name' => 'num_doc_pac',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 20)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
