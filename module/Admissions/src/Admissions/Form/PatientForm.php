<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Form;

use Zend\Form\Form;
use Zend\Db\ResultSet\ResultSet;
use Admissions\Model\Entity\Document;
use Admissions\Model\Entity\DocumentTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class PatientForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('patients');

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
            'name' => 'pri_nom_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Primer nombre',
            ),
            'attributes' => array(
                'placeholder' => 'primer nombre',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 3,
                'maxlength' => 100,
            ),
        ));

        $this->add(array(
            'name' => 'seg_nom_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Segundo nombre',
            ),
            'attributes' => array(
                'placeholder' => 'segundo nombre',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 100,
            ),
        ));

        $this->add(array(
            'name' => 'pri_ape_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Primer apellido',
            ),
            'attributes' => array(
                'placeholder' => 'primer apellido',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 100,
            ),
        ));

        $this->add(array(
            'name' => 'seg_ape_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Segundo apellido',
            ),
            'attributes' => array(
                'placeholder' => 'segundo apellido',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 100,
            ),
        ));

        $this->add(array(
            'name' => 'fec_nac_pac',
            'type' => 'date',
            'options' => array(
                'label' => 'Fecha de nacimiento',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'dir_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Dirección de residencia',
            ),
            'attributes' => array(
                'placeholder' => 'dirección',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 200,
            ),
        ));

        $this->add(array(
            'name' => 'num_tel_pac',
            'type' => 'number',
            'options' => array(
                'label' => 'Número de teléfono',
            ),
            'attributes' => array(
                'placeholder' => 'teléfono',
                'class' => 'form-control input-sm',
                'min' => 1000000,
                'max' => 1000000000,
            ),
        ));

        $this->add(array(
            'name' => 'num_cel_pac',
            'type' => 'number',
            'options' => array(
                'label' => 'Número de móvil',
            ),
            'attributes' => array(
                'placeholder' => 'móvil',
                'class' => 'form-control input-sm',
                'min' => 1000000000,
                'max' => 1000000000000,
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
