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

class Patologia implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $num_fol;
	public $material_estudio;
    public $diagnostico_clinico;
    public $descripcion_macroscopica;
    public $descripcion_microscopica;
    public $diagnostico;
    public $nota;
    
    
	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->num_fol = (isset($data["num_fol"])) ? $data["num_fol"] : null;
		$this->material_estudio = (isset($data["material_estudio"])) ? $data["material_estudio"] : null;
        $this->diagnostico_clinico = (isset($data["diagnostico_clinico"])) ? $data["diagnostico_clinico"] : null;
        $this->descripcion_macroscopica = (isset($data["descripcion_macroscopica"])) ? $data["descripcion_macroscopica"] : null;
        $this->descripcion_microscopica = (isset($data["descripcion_microscopica"])) ? $data["descripcion_microscopica"] : null;
        $this->diagnostico = (isset($data["diagnostico"])) ? $data["diagnostico"] : null;
        $this->nota = (isset($data["nota"])) ? $data["nota"] : null;
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
				'name' => 'material_estudio',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));
            
			$inputFilter->add(array(
				'name' => 'diagnostico_clinico',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));
            
            
			$inputFilter->add(array(
				'name' => 'descripcion_macroscopica',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));
            
			$inputFilter->add(array(
				'name' => 'descripcion_microscopica',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));
            
			$inputFilter->add(array(
				'name' => 'diagnostico',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));
            
			$inputFilter->add(array(
				'name' => 'nota',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					'StringLength' => new \Zend\Validator\StringLength(array('min' => 0, 'max' => 5000)),
				),
			));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}
