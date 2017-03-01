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

class DiagnosticForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('first_history');

        $this->add(array(
            'name' => 'cod_dia',
            'type' => 'text',
            'options' => array(
                'label' => 'Diagnóstico',
            ),
            'attributes' => array(
                'id' => 'diagnostic_id',
                'placeholder' => 'código diagnóstico',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'data-resource' => '',
                'minlength' => 1,
                'maxlength' => 6,
            ),
        ));

        $this->add(array(
            'name' => 'nom_dia',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre del diagnóstico',
            ),
            'attributes' => array(
                'id' => 'diagnosticSuggestion',
                'placeholder' => 'diagnóstico',
                'required' => 'required',
                'autofocus' => 'autofocus',
                'class' => 'form-control input-sm',
                'data-resource' => '',
                'minlength' => 4,
                'maxlength' => 1000,
            ),
        ));

        $this->add(array(
            'name' => 'dia_pri',
            'type' => 'radio',
            'options' => array(
                'label' => 'Diagnóstico principal',
                'value_options' => array(
                    0 => "No",
                    1 => "Si",
                ),
            ),
            'attributes' => array(
                "value" => 1
            ),
        ));

        $this->add(array(
            'name' => 'tip_dia',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo',
                'value_options' => array(
                    1 => "Presuntivo",
                    2 => "Definitivo",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                "value" => 0
            ),
        ));

        $this->add(array(
            'name' => 'clasi_dia',
            'type' => 'select',
            'options' => array(
                'label' => 'Clasificación',
                'value_options' => array(
                    1 => "Impresión diagnóstico",
                    2 => "Confirmado nuevo",
                    3 => "Confirmado repetido",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                "value" => 0
            ),
        ));

        $this->add(array(
            'name' => 'clase_dia',
            'type' => 'select',
            'options' => array(
                'label' => 'Clase',
                'value_options' => array(
                    1 => "Preoperatorio",
                    2 => "Postoperatorio",
                    3 => "Histopatológico",
                    4 => "No corresponde",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                "value" => 0
            ),
        ));

        $this->add(array(
            'name' => 'dia_ing',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Ingreso',
            ),
        ));

        $this->add(array(
            'name' => 'dia_egr',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Egreso',
            ),
        ));

        $this->add(array(
            'name' => 'obs_dia',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Observaciones',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '500',
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
