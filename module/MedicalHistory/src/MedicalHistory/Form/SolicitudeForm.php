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

class SolicitudeForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('add_solicitude');

        $this->add(array(
            'name' => 'cod_exa',
            'type' => 'text',
            'options' => array(
                'label' => 'Código de la solicitud',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'maxlength' => '7',
            ),
        ));

        $this->add(array(
            'name' => 'nom_exa',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '200',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'tip_exa',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo de exámen',
                'value_options' => array(
                    1 => "Exámenes",
                    2 => "Procedimientos Quirúrgicos",
                    3 => "Procedimientos no Quirúrgicos",
                    4 => "Patologías",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'can_exa',
            'type' => 'number',
            'options' => array(
                'label' => 'Cantidad',
            ),
            'attributes' => array(
                "class" => 'form-control input-sm',
                'required' => 'required',
                'min' => '1',
                'value' => '1'
            ),
        ));

        $this->add(array(
            'name' => 'est_exa',
            'type' => 'select',
            'options' => array(
                'label' => 'Estado',
                'value_options' => array(
                    1 => "Rutinario",
                    2 => "Urgente",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'obs_exa',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Observaciones',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '400',
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
