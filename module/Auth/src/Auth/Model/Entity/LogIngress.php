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

class LogIngress implements InputFilterAwareInterface
{
	public $log_ingresos_id;
	public $cod_usu;
	public $fec_ing;
	public $ip_address;

	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->log_ingresos_id = (isset($data["log_ingresos_id"])) ? $data["log_ingresos_id"] : null;
		$this->cod_usu = (isset($data["cod_usu"])) ? $data["cod_usu"] : null;
		$this->fec_ing = (isset($data["fec_ing"])) ? $data["fec_ing"] : null;
		$this->ip_address = (isset($data["ip_address"])) ? $data["ip_address"] : null;
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

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
