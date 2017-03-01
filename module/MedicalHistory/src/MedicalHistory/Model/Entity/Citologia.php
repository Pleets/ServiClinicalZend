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

class Citologia implements InputFilterAwareInterface
{
	public $cod_tip_doc;
	public $num_doc_pac;
	public $num_fol;
	public $cal_mues_a;
    public $cal_mues_b;
	public $cal_mues_c;
	public $cal_mues_d;
	public $cat_gen_a;
	public $cat_gen_b;
	public $mic_a;
	public $mic_b;
	public $mic_c;
	public $mic_d;
	public $mic_e;
	public $mic_f;
	public $mic_g;
	public $otr_haz_a;
	public $otr_haz_b;
	public $otr_haz_c;
	public $otr_haz_d;
	public $otr_haz_e;
	public $otr_haz_f;
	public $ano_cel_esc_a;
	public $ano_cel_esc_b;
	public $ano_cel_esc_c;
	public $ano_cel_esc_d;
	public $ano_cel_esc_e;
	public $ano_cel_esc_f;
	public $ano_cel_gla_a;
	public $ano_cel_gla_b;
	public $ano_cel_gla_c;
	public $ano_cel_gla_d;
	public $ano_cel_gla_e;
	public $ano_cel_gla_f;
	public $ano_cel_gla_g;
	public $ano_cel_gla_h;
	public $observaciones;


	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->cod_tip_doc = (isset($data["cod_tip_doc"])) ? $data["cod_tip_doc"] : null;
		$this->num_doc_pac = (isset($data["num_doc_pac"])) ? $data["num_doc_pac"] : null;
		$this->num_fol = (isset($data["num_fol"])) ? $data["num_fol"] : null;
		$this->cal_mues_a = (isset($data["cal_mues_a"])) ? $data["cal_mues_a"] : null;

		$this->cal_mues_b= (isset($data["cal_mues_b"])) ? $data["cal_mues_b"] : null;
		$this->cal_mues_c= (isset($data["cal_mues_c"])) ? $data["cal_mues_c"] : null;
		$this->cal_mues_d= (isset($data["cal_mues_d"])) ? $data["cal_mues_d"] : null;
		$this->cat_gen_a= (isset($data["cat_gen_a"])) ? $data["cat_gen_a"] : null;
		$this->cat_gen_b= (isset($data["cat_gen_b"])) ? $data["cat_gen_b"] : null;
		$this->mic_a= (isset($data["mic_a"])) ? $data["mic_a"] : null;
		$this->mic_b= (isset($data["mic_b"])) ? $data["mic_b"] : null;
		$this->mic_c= (isset($data["mic_c"])) ? $data["mic_c"] : null;
		$this->mic_d= (isset($data["mic_d"])) ? $data["mic_d"] : null;
		$this->mic_e= (isset($data["mic_e"])) ? $data["mic_e"] : null;
		$this->mic_f= (isset($data["mic_f"])) ? $data["mic_f"] : null;
		$this->mic_g= (isset($data["mic_g"])) ? $data["mic_g"] : null;
		$this->otr_haz_a= (isset($data["otr_haz_a"])) ? $data["otr_haz_a"] : null;
		$this->otr_haz_b= (isset($data["otr_haz_b"])) ? $data["otr_haz_b"] : null;
		$this->otr_haz_c= (isset($data["otr_haz_c"])) ? $data["otr_haz_c"] : null;
		$this->otr_haz_d= (isset($data["otr_haz_d"])) ? $data["otr_haz_d"] : null;
		$this->otr_haz_e= (isset($data["otr_haz_e"])) ? $data["otr_haz_e"] : null;
		$this->otr_haz_f= (isset($data["otr_haz_f"])) ? $data["otr_haz_f"] : null;
		$this->ano_cel_esc_a= (isset($data["ano_cel_esc_a"])) ? $data["ano_cel_esc_a"] : null;
		$this->ano_cel_esc_b= (isset($data["ano_cel_esc_b"])) ? $data["ano_cel_esc_b"] : null;
		$this->ano_cel_esc_c= (isset($data["ano_cel_esc_c"])) ? $data["ano_cel_esc_c"] : null;
		$this->ano_cel_esc_d= (isset($data["ano_cel_esc_d"])) ? $data["ano_cel_esc_d"] : null;
		$this->ano_cel_esc_e= (isset($data["ano_cel_esc_e"])) ? $data["ano_cel_esc_e"] : null;
		$this->ano_cel_esc_f= (isset($data["ano_cel_esc_f"])) ? $data["ano_cel_esc_f"] : null;
		$this->ano_cel_gla_a= (isset($data["ano_cel_gla_a"])) ? $data["ano_cel_gla_a"] : null;
		$this->ano_cel_gla_b= (isset($data["ano_cel_gla_b"])) ? $data["ano_cel_gla_b"] : null;
		$this->ano_cel_gla_c= (isset($data["ano_cel_gla_c"])) ? $data["ano_cel_gla_c"] : null;
		$this->ano_cel_gla_d= (isset($data["ano_cel_gla_d"])) ? $data["ano_cel_gla_d"] : null;
		$this->ano_cel_gla_e= (isset($data["ano_cel_gla_e"])) ? $data["ano_cel_gla_e"] : null;
		$this->ano_cel_gla_f= (isset($data["ano_cel_gla_f"])) ? $data["ano_cel_gla_f"] : null;
		$this->ano_cel_gla_g= (isset($data["ano_cel_gla_g"])) ? $data["ano_cel_gla_g"] : null;
		$this->ano_cel_gla_h= (isset($data["ano_cel_gla_h"])) ? $data["ano_cel_gla_h"] : null;
		$this->observaciones = (isset($data["observaciones"])) ? $data["observaciones"] : null;

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
				'name' => 'cal_mues_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'cal_mues_b',
				'required' => false

			));


			$inputFilter->add(array(
				'name' => 'cal_mues_c',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'cal_mues_d',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'cat_gen_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'cat_gen_b',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_b',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_c',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_d',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_e',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_f',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'mic_g',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_b',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_c',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_d',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_e',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'otr_haz_f',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_b',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_c',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_d',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_e',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_esc_f',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_a',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_b',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_c',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_d',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_e',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_f',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_g',
				'required' => false

			));

			$inputFilter->add(array(
				'name' => 'ano_cel_gla_h',
				'required' => false

			));


			$inputFilter->add(array(
				'name' => 'observaciones',
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
