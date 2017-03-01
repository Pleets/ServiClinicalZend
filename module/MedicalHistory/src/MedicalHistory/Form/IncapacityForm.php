<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Form;

use Zend\Form\Form;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class IncapacityForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('add_incapacity');

        $this->add(array(
            'name' => 'cod_dia',
            'type' => 'text',
            'options' => array(
                'label' => 'Código del diagnóstico',
            ),
            'attributes' => array(
                'id' => 'incapacities_diagnostic_id',
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'required' => 'required',
                'minlength' => '1',
                'maxlength' => '6',
            ),
        ));

        $this->add(array(
            'name' => 'nom_dia',
            'type' => 'text',
            'options' => array(
                'label' => 'Diagnóstico',
            ),
            'attributes' => array(
                'id' => 'incapacitiesDiagnosticSuggestion',
                'placeholder' => 'medicamento',
                'autofocus' => 'autofocus',
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '1000',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'num_dia_inc',
            'type' => 'number',
            'options' => array(
                'label' => 'Número de días',
            ),
            'attributes' => array(
                "class" => 'form-control input-sm',
                'required' => 'required',
                'min' => '1',
            ),
        ));

        $this->add(array(
            'name' => 'fec_ini_inc',
            'type' => 'date',
            'options' => array(
                'label' => 'Fecha de inicio',
            ),
            'attributes' => array(
                "class" => 'form-control input-sm',
                'required' => 'required',
                'value' => date("Y-m-d")
            ),
        ));

        $this->add(array(
            'name' => 'des_inc',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Descripción',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '4000',
                'rows' => '3',
                'cols' => '100',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
            ),
        ));
    }
}
