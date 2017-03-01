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

class Admission implements InputFilterAwareInterface
{
	public $cod_adm;
	public $fec_adm;
	public $est_adm;
	public $cod_tip_doc;
	public $num_doc_pac;
	public $cod_usu_med;
	public $cod_are;
	public $cod_ent;
	public $obs_adm;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_adm = (isset($data["cod_adm"])) ? $data["cod_adm"] : null;
		$this->fec_adm = (isset($data["fec_adm"])) ? $data["fec_adm"] : null;
		$this->est_adm = (isset($data["est_adm"])) ? $data["est_adm"] : null;
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->cod_usu_med = (isset($data["cod_usu_med"])) ? $data["cod_usu_med"] : null;
		$this->cod_are = (isset($data["cod_are"])) ? $data["cod_are"] : null;
		$this->cod_ent = (isset($data["cod_ent"])) ? $data["cod_ent"] : null;
		$this->obs_adm = (isset($data["obs_adm"])) ? $data["obs_adm"] : null;
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
				'name' => 'cod_adm',
				'required' => true,
				'validators' => array(
					'Between' => new \Zend\Validator\Between(array('min' => 1, 'max' => '18446744073709551615')),
				),
			));

			$inputFilter->add(array(
				'name' => 'fec_adm',
				'required' => true,
				'validators' => array(
					'Date' => new \Zend\Validator\Date(),
				),
			));

			$inputFilter->add(array(
				'name' => 'est_adm',
				'required' => true,
				'validators' => array(
					'Haystack' => new \Zend\Validator\InArray(array('haystack' => array('', 'A', 'F'))),
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
				'name' => 'cod_are',
				'required' => true,
				'validators' => array(
					'Between' => new \Zend\Validator\Between(array('min' => 1, 'max' => '4294967295')),
				),
			));

			$inputFilter->add(array(
				'name' => 'cod_ent',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 3, 'max' => 10)),
				),
			));

			$inputFilter->add(array(
				'name' => 'obs_adm',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 2000)),
				),
			));

			$inputFilter->add(array(
				'name' => 'cod_usu_med',
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
