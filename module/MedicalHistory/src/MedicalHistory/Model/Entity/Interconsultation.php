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

class Interconsultation implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $num_fol;

	public $cod_int;
	public $cod_dia;
	public $cod_esp;
	public $mot_int;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->num_fol = (isset($data["num_fol"])) ? $data["num_fol"] : null;

		$this->cod_int = (isset($data["cod_int"])) ? $data["cod_int"] : null;
		$this->cod_dia = (isset($data["cod_dia"])) ? $data["cod_dia"] : null;
		$this->cod_esp = (isset($data["cod_esp"])) ? $data["cod_esp"] : null;
		$this->mot_int = (isset($data["mot_int"])) ? $data["mot_int"] : null;
	}

	public function exchangeJson($data = null)
	{
		$this->cod_tip_doc = (isset($data->cod_tip_doc)) ? $data->cod_tip_doc : null;
		$this->num_doc_pac = (isset($data->num_doc_pac)) ? $data->num_doc_pac : null;
		$this->num_fol = (isset($data->num_fol)) ? $data->num_fol : null;

		$this->cod_int = (isset($data->cod_int)) ? $data->cod_int : null;
		$this->cod_dia = (isset($data->cod_dia)) ? $data->cod_dia : null;
		$this->nom_dia = (isset($data->nom_dia)) ? $data->nom_dia : null;
		$this->cod_esp = (isset($data->cod_esp)) ? $data->cod_esp : null;
		$this->nom_esp = (isset($data->nom_esp)) ? $data->nom_esp : null;
		$this->mot_int = (isset($data->mot_int)) ? $data->mot_int : null;
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
				'name' => 'cod_int',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 1, 'max' => 7)),
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
			));

			$inputFilter->add(array(
				'name' => 'cod_esp',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

			$inputFilter->add(array(
				'name' => 'nom_esp',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 4, 'max' => 200)),
				),
			));

			$inputFilter->add(array(
				'name' => 'mot_int',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 3000)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
