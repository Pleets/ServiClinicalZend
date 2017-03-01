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

class EntityForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('exams');

        $this->add(array(
            'name' => 'cod_ent',
            'type' => 'text',
            'options' => array(
                'label' => 'Código de la entidad',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'minlength' => '3',
                'maxlength' => '10',
            ),
        ));

        $this->add(array(
            'name' => 'nom_ent',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre',
                'required' => 'required',
                'minlength' => '3',
                'maxlength' => '300',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'dir_ent',
            'type' => 'text',
            'options' => array(
                'label' => 'Dirección',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'dirección',
                'minlength' => '0',
                'maxlength' => '200',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'est_ent',
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
