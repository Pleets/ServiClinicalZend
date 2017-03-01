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

class Medication implements InputFilterAwareInterface
{
	public $cod_med;
	public $nom_med;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_med = (isset($data["cod_med"])) ? $data["cod_med"] : null;
		$this->nom_med = (isset($data["nom_med"])) ? $data["nom_med"] : null;
	}

	public function exchangeJson($data = null)
	{
		$this->cod_med = (isset($data->cod_med)) ? $data->cod_med : null;
		$this->nom_med = (isset($data->nom_med)) ? $data->nom_med : null;
		$this->can_med = (isset($data->can_med)) ? $data->can_med : null;
		$this->ter_med = (isset($data->term_med)) ? $data->term_med : null;
		$this->num_dia = (isset($data->num_dia)) ? $data->num_dia : null;
		$this->cod_apl_med = (isset($data->cod_apl_med)) ? $data->cod_apl_med : null;
		$this->pos_med = (isset($data->pos_med)) ? $data->pos_med : null;
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

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
