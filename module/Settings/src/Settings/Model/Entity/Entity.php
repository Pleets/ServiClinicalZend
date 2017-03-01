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

class Entity implements InputFilterAwareInterface
{
	public $cod_ent;
	public $nom_ent;
	public $dir_ent;
	public $est_ent;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_ent = (isset($data["cod_ent"])) ? $data["cod_ent"] : null;
		$this->nom_ent = (isset($data["nom_ent"])) ? $data["nom_ent"] : null;
		$this->dir_ent = (isset($data["dir_ent"])) ? $data["dir_ent"] : null;
		$this->est_ent = (isset($data["est_ent"])) ? $data["est_ent"] : null;
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
				'name' => 'nom_ent',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 3, 'max' => 300)),
				),
			));

			$inputFilter->add(array(
				'name' => 'dir_ent',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'Alnum' => new \Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true)),
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 200)),
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
