<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Model\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface
{
	public $cod_usu;
	public $nom_usu;
	public $cod_per;
	public $fec_reg_usu;
	public $est_usu;
	public $pas_usu;
	public $fir_usu;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_usu = (isset($data["cod_usu"])) ? $data["cod_usu"] : null;
		$this->nom_usu = (isset($data["nom_usu"])) ? $data["nom_usu"] : null;
		$this->cod_per = (isset($data["cod_per"])) ? $data["cod_per"] : null;
		$this->fec_reg_usu = (isset($data["fec_reg_usu"])) ? $data["fec_reg_usu"] : null;
		$this->est_usu = (isset($data["est_usu"])) ? $data["est_usu"] : null;
		$this->pas_usu = (isset($data["pas_usu"])) ? $data["pas_usu"] : null;
		$this->fir_usu = (isset($data["fir_usu"])) ? $data["fir_usu"] : null;
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
				'name' => 'cod_usu',
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
				'name' => 'nom_usu',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 50)),
				),
			));

			$inputFilter->add(array(
				'name' => 'pas_usu',
				'required' => true,
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 8, 'max' => 50)),
				),
			));

			$inputFilter->add(array(
				'name' => 'cod_per',
				'required' => false,
				'validators' => array(
					'values' => new \Zend\Validator\InArray(array('haystack' => array(1, 2, 3, 4, 5, 6))),
				),
			));

			$inputFilter->add(array(
				'name' => 'est_usu',
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
