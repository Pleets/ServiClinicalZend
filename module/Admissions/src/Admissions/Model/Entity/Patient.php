<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Model\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Patient implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $pri_nom_pac;
	public $seg_nom_pac;
	public $pri_ape_pac;
	public $seg_ape_pac;
	public $fec_nac_pac;
	public $dir_pac;
	public $num_tel_pac;
	public $num_cel_pac;
	public $cod_usu_reg;
	public $fec_reg_pac;
	public $cod_usu_mod;
	public $fec_mod_pac;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->pri_nom_pac = (isset($data["pri_nom_pac"])) ? $data["pri_nom_pac"] : null;
		$this->seg_nom_pac = (isset($data["seg_nom_pac"])) ? $data["seg_nom_pac"] : null;
		$this->pri_ape_pac = (isset($data["pri_ape_pac"])) ? $data["pri_ape_pac"] : null;
		$this->seg_ape_pac = (isset($data["seg_ape_pac"])) ? $data["seg_ape_pac"] : null;
		$this->fec_nac_pac = (isset($data["fec_nac_pac"])) ? $data["fec_nac_pac"] : null;
		$this->dir_pac = (isset($data["dir_pac"])) ? $data["dir_pac"] : null;
		$this->num_tel_pac = (isset($data["num_tel_pac"])) ? $data["num_tel_pac"] : null;
		$this->num_cel_pac = (isset($data["num_cel_pac"])) ? $data["num_cel_pac"] : null;
		$this->cod_usu_reg = (isset($data["cod_usu_reg"])) ? $data["cod_usu_reg"] : null;
		$this->fec_reg_pac = (isset($data["fec_reg_pac"])) ? $data["fec_reg_pac"] : null;
		$this->cod_usu_mod = (isset($data["cod_usu_mod"])) ? $data["cod_usu_mod"] : null;
		$this->fec_mod_pac = (isset($data["fec_mod_pac"])) ? $data["fec_mod_pac"] : null;
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

			$inputFilter->add(array(
				'name' => 'pri_nom_pac',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 3, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'seg_nom_pac',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'pri_ape_pac',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'seg_ape_pac',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'fec_nac_pac',
				'required' => true,
				'validators' => array(
					'Date' => new \Zend\Validator\Date(),
				),
			));

			$inputFilter->add(array(
				'name' => 'dir_pac',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 200)),
				),
			));

			$inputFilter->add(array(
				'name' => 'num_tel_pac',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 7, 'max' => 10)),
				),
			));

			$inputFilter->add(array(
				'name' => 'num_cel_pac',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 10, 'max' => 13)),
				),
			));

			$inputFilter->add(array(
				'name' => 'cod_usu_reg',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 20)),
				),
			));

			$inputFilter->add(array(
				'name' => 'fec_reg_pac',
				'required' => true,
				'validators' => array(
					'Date' => new \Zend\Validator\Date(),
				),
			));

			$inputFilter->add(array(
				'name' => 'cod_usu_mod',
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 20)),
				),
			));

			$inputFilter->add(array(
				'name' => 'fec_mod_pac',
				'validators' => array(
					'Date' => new \Zend\Validator\Date(),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
