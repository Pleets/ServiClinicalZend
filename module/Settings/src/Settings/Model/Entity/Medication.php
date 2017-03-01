<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Settings\Model\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Medication implements InputFilterAwareInterface
{
	public $cod_med;
	public $nom_med;
	public $nom_gen;
	public $con_med;
	public $pre_med;
	public $est_med;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_med = (isset($data["cod_med"])) ? $data["cod_med"] : null;
		$this->nom_med = (isset($data["nom_med"])) ? $data["nom_med"] : null;
		$this->nom_gen = (isset($data["nom_gen"])) ? $data["nom_gen"] : null;
		$this->con_med = (isset($data["con_med"])) ? $data["con_med"] : null;
		$this->pre_med = (isset($data["pre_med"])) ? $data["pre_med"] : null;
		$this->est_med = (isset($data["est_med"])) ? $data["est_med"] : null;
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
				'name' => 'cod_med',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

			$inputFilter->add(array(
				'name' => 'nom_med',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 200)),
				),
			));

			$inputFilter->add(array(
				'name' => 'nom_gen',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 200)),
				),
			));

			$inputFilter->add(array(
				'name' => 'con_med',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'pre_med',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 100)),
				),
			));

			$inputFilter->add(array(
				'name' => 'est_med',
				'required' => true,
				'validators' => array(
					'Between' => new \Zend\Validator\InArray(array('haystack' => array(1, 2))),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
