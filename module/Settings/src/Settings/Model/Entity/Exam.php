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

class Exam implements InputFilterAwareInterface
{
	public $cod_exa;
	public $nom_exa;
	public $est_exa;
	public $tip_exa;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_exa = (isset($data["cod_exa"])) ? $data["cod_exa"] : null;
		$this->nom_exa = (isset($data["nom_exa"])) ? $data["nom_exa"] : null;
		$this->est_exa = (isset($data["est_exa"])) ? $data["est_exa"] : null;
		$this->tip_exa = (isset($data["tip_exa"])) ? $data["tip_exa"] : null;
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
				'name' => 'cod_exa',
				'required' => true,
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 1, 'max' => 7)),
				),
			));

			$inputFilter->add(array(
				'name' => 'nom_exa',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 3, 'max' => 200)),
				),
			));

			$inputFilter->add(array(
				'name' => 'est_exa',
				'required' => true,
				'validators' => array(
					'Between' => new \Zend\Validator\InArray(array('haystack' => array(1, 2))),
				),
			));

			$inputFilter->add(array(
				'name' => 'tip_exa',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
