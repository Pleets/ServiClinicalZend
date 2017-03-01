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

class MedicationForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('add_medication');

        $this->add(array(
            'name' => 'cod_med',
            'type' => 'text',
            'options' => array(
                'label' => 'Código del medicamento',
            ),
            'attributes' => array(
                'id' => 'medication_id',
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
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
                'id' => 'medicationSuggestion',
                'placeholder' => 'medicamento',
                'autofocus' => 'autofocus',
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '200',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'can_med',
            'type' => 'number',
            'options' => array(
                'label' => 'Cantidad',
            ),
            'attributes' => array(
                "class" => 'form-control input-sm',
                'required' => 'required',
                'min' => '1',
            ),
        ));

        $this->add(array(
            'name' => 'cod_apl_med',
            'type' => 'select',
            'options' => array(
                'label' => 'Aplicación',
                'value_options' => array(
                    1 => "Endovenosa",
                    2 => "Enteral",
                    3 => "Infusión",
                    4 => "Intradérmica",
                    5 => "Intramuscular",
                    6 => "Intrarectal",
                    7 => "Intravenosa",
                    8 => "Oftalmica",
                    9 => "Oral",
                    10 => "Oral sonda",
                    11 => "Oral succión",
                    12 => "Otros",
                    13 => "Parenteral",
                    14 => "Respiratoria",
                    15 => "Subcutánea",
                    16 => "Todas las vías",
                    17 => "Tópico",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'ter_med',
            'type' => 'select',
            'options' => array(
                'label' => 'Terminación',
                'value_options' => array(
                    1 => "Definida",
                    2 => "Indefinida",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'num_dia',
            'type' => 'number',
            'options' => array(
                'label' => 'Número de días',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'required' => 'required',
                'min' => '1',
                'value' => 30
            ),
        ));

        $this->add(array(
            'name' => 'pos_med',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Posología',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '3000',
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
