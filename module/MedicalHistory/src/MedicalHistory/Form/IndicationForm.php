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

class IndicationForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('first_history');

        $this->add(array(
            'name' => 'cod_ind',
            'type' => 'number',
            'options' => array(
                'label' => 'código del indicador',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'indicacion',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Detalle de la indicación',
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
                'class' => 'form-control input-sm',
                'maxlength' => 10000,
                'rows' => 3
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
