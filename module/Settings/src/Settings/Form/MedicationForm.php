<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Settings\Form;

use Zend\Form\Form;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class MedicationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('exams');

        $this->add(array(
            'name' => 'cod_med',
            'type' => 'text',
            'options' => array(
                'label' => 'Código del medicamento',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'minlength' => '1',
                'maxlength' => '11',
            ),
        ));

        $this->add(array(
            'name' => 'nom_med',
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
            'name' => 'nom_gen',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre genérico',
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
            'name' => 'con_med',
            'type' => 'text',
            'options' => array(
                'label' => 'Concentración',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'Concentración',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '100',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'pre_med',
            'type' => 'text',
            'options' => array(
                'label' => 'Presentación',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'presentación',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '100',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'est_med',
            'type' => 'select',
            'options' => array(
                'label' => 'Estado',
                'value_options' => array(
                    '1' => 'Activo',
                    '2' => 'Inactivo'
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => '1',
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-sm',
            ),
        ));

    }
}
