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

class TypeHistory implements InputFilterAwareInterface
{
	public $cod_tip_his;
	public $nom_tip_his;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_his = (isset($data["cod_tip_his"])) ? $data["cod_tip_his"] : null;
		$this->nom_tip_his = (isset($data["nom_tip_his"])) ? $data["nom_tip_his"] : null;
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
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
