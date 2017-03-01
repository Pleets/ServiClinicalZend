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
use MedicalHistory\Model\Entity\Specialty;
use MedicalHistory\Model\Entity\SpecialtyTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class InterconsultationForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('first_history');

        $dbAdapter = $controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $tableGateway = new TableGateway('hcneuro_especialidades', $dbAdapter);

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Specialty());

        $specialtyTable = new SpecialtyTable($tableGateway, $dbAdapter, null, $resultSetPrototype);

        $result = $specialtyTable->fetchAll()->toArray();

        $specialties = array();
        foreach ($result as $specialty) {
            $specialties[$specialty["cod_esp"]] = $specialty["nom_esp"];
        }

        $this->add(array(
            'name' => 'cod_int',
            'type' => 'number',
            'options' => array(
                'label' => 'código de la interconsulta',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'cod_dia',
            'type' => 'text',
            'options' => array(
                'label' => 'Diagnóstico',
            ),
            'attributes' => array(
                'id' => 'interconsultation_diagnostic_id',
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
                'id' => 'interconsultationDiagnosticSuggestion',
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
            'name' => 'cod_esp',
            'type' => 'text',
            'options' => array(
                'label' => 'Especialidad',
                'value_options' => $specialties
            ),
            'attributes' => array(
                'id' => 'interconsultation_specialty_id',
                'class' => 'form-control input-sm',
                'placeholder' => 'código especialidad',
                'required' => 'required',
                'data-resource' => '',
                'minlength' => 1,
                'maxlength' => 4,
            ),
        ));

        $this->add(array(
            'name' => 'nom_esp',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre de la especialidad',
            ),
            'attributes' => array(
                'id' => 'interconsultationSpecialtySuggestion',
                'placeholder' => 'especialidad',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'data-resource' => '',
                'minlength' => 4,
                'maxlength' => 1000,
            ),
        ));

        $this->add(array(
            'name' => 'mot_int',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Motivo de la interconsulta',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => 3000,
                'rows' => 4
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
