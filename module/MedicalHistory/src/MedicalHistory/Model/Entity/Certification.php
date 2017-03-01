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

class Certification implements InputFilterAwareInterface
{
	public $cod_cer;
	public $tit_cer;
	public $des_cer;
	public $cod_adm;
	public $cod_tip_doc;
	public $num_doc_pac;
	public $fec_reg;
	public $cod_usu;
	public $usu_med;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_cer = (isset($data["cod_cer"])) ? $data["cod_cer"] : null;
		$this->tit_cer = (isset($data["tit_cer"])) ? $data["tit_cer"] : null;
		$this->des_cer = (isset($data["des_cer"])) ? $data["des_cer"] : null;
		$this->cod_adm = (isset($data["cod_adm"])) ? $data["cod_adm"] : null;
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->fec_reg = (isset($data["fec_reg"])) ? $data["fec_reg"] : date("Y-m-d");
		$this->cod_usu = (isset($data["cod_usu"])) ? $data["cod_usu"] : null;
		$this->usu_med = (isset($data["usu_med"])) ? $data["usu_med"] : null;
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
				'name' => 'cod_cer',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

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

			$inputFilter->add(array(
				'name' => 'num_fol',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

			$inputFilter->add(array(
				'name' => 'tit_cer',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('max' => 600)),
				),
			));

			$inputFilter->add(array(
				'name' => 'des_cer',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('max' => 5000)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
