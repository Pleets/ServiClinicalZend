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

class ExamForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('exams');

        $this->add(array(
            'name' => 'cod_exa',
            'type' => 'text',
            'options' => array(
                'label' => 'Código de exámen',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'minlength' => '1',
                'maxlength' => '7',
            ),
        ));

        $this->add(array(
            'name' => 'nom_exa',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre del exámen',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre',
                'required' => 'required',
                'minlength' => '3',
                'maxlength' => '200',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'est_exa',
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
            'name' => 'tip_exa',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo de exámen',
                'value_options' => array(
                    '1' => 'Exámenes',
                    '2' => 'Patologías',
                    '3' => 'Procedimientos Quirúrgicos',
                    '4' => 'Procedimientos no Quirúrgicos',
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
