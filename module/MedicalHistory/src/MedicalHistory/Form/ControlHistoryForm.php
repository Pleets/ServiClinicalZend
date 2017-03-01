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
use Admissions\Model\Entity\Document;
use Admissions\Model\Entity\DocumentTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class ControlHistoryForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('control_history');

        $dbAdapter = $controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $tableGateway = new TableGateway('hcneuro_tipodoc', $dbAdapter);

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Document());

        $documentTable = new DocumentTable($tableGateway, $dbAdapter, null, $resultSetPrototype);

        $result = $documentTable->fetchAll()->toArray();

        $documents = array();
        foreach ($result as $document) {
            $documents[$document["cod_tip_doc"]] = $document["nom_tip_doc"];
        }

        $this->add(array(
            'name' => 'cod_tip_doc',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo de documento',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'num_doc_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Documento',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => 'documento',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 20,
            ),
        ));

        $this->add(array(
            'name' => 'tip_evo',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo evolución',
                'value_options' => array(
                    '1' => 'CONTROL MEDICINA GENERAL',
                    '2' => 'CONTROL MEDICINA ESPECIALIZADA',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm'
            ),
        ));

        $this->add(array(
            'name' => 'inf_sub',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Información subjetiva',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '4',
                'cols' => '200'
            ),
        ));

        $this->add(array(
            'name' => 'tas',
            'type' => 'number',
            'options' => array(
                'label' => 'TAS',
            ),
            'attributes' => array(
                'placeholder' => 'mmHg',
                'class' => 'form-control input-sm',
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'tad',
            'type' => 'number',
            'options' => array(
                'label' => 'TAD',
            ),
            'attributes' => array(
                'placeholder' => 'mmHg',
                'class' => 'form-control input-sm',
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'tam',
            'type' => 'number',
            'options' => array(
                'label' => 'TAM',
            ),
            'attributes' => array(
                'placeholder' => '',
                'class' => 'form-control input-sm',
                'step' => 0.1,
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'fc',
            'type' => 'number',
            'options' => array(
                'label' => 'FC',
            ),
            'attributes' => array(
                'placeholder' => 'l/min',
                'class' => 'form-control input-sm',
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'fr',
            'type' => 'number',
            'options' => array(
                'label' => 'FR',
            ),
            'attributes' => array(
                'placeholder' => 'FR',
                'class' => 'form-control input-sm',
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'tem',
            'type' => 'number',
            'options' => array(
                'label' => 'TEM',
            ),
            'attributes' => array(
                'placeholder' => '°C',
                'class' => 'form-control input-sm',
                'min' => 0,
                'max' => 10000,
            ),
        ));

        $this->add(array(
            'name' => 'hal_exa',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Hallazgos al exámen físico',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '4',
                'cols' => '40',
            ),
        ));

        $this->add(array(
            'name' => 'int_par',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Interpretación paraclínicos',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '4',
                'cols' => '40',
            ),
        ));

        $this->add(array(
            'name' => 'ana_con',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Análisis y conducta',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '4',
                'cols' => '40',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' =>'Guardar'
            ),
        ));

    }
}
