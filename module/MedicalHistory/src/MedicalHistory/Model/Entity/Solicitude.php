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

class Solicitude implements InputFilterAwareInterface
{
	public $cod_exa;
	public $nom_exa;
	public $tip_exa;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_exa = (isset($data["cod_exa"])) ? $data["cod_exa"] : null;
		$this->nom_exa = (isset($data["nom_exa"])) ? $data["nom_exa"] : null;
		$this->tip_exa = (isset($data["tip_exa"])) ? $data["tip_exa"] : null;
	}

	public function exchangeJson($data = null)
	{
		$this->cod_tip_doc = (isset($data->cod_tip_doc)) ? $data->cod_tip_doc : null;
		$this->num_doc_pac = (isset($data->num_doc_pac)) ? $data->num_doc_pac : null;
		$this->num_fol = (isset($data->num_fol)) ? $data->num_fol : null;

		$this->cod_exa = (isset($data->cod_exa)) ? $data->cod_exa : null;
		$this->nom_exa = (isset($data->nom_exa)) ? $data->nom_exa : null;
		$this->tip_exa = (isset($data->tip_exa)) ? $data->tip_exa : null;
		$this->can_exa = (isset($data->can_exa)) ? $data->can_exa : null;
		$this->est_exa = (isset($data->est_exa)) ? $data->est_exa : null;
		$this->obs_exa = (isset($data->obs_exa)) ? $data->obs_exa : null;
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
				'name' => 'can_exa',
				'required' => true,
				'validators' => array(
					'Digits' => new \Zend\Validator\Digits(),
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
					'Between' => new \Zend\Validator\InArray(array('haystack' => array(1, 2, 3, 4))),
				),
			));

			$inputFilter->add(array(
				'name' => 'obs_exa',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 400)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
