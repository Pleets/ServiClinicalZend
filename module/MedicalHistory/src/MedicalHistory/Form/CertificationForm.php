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

use Admissions\Model\Entity\Document;
use Admissions\Model\Entity\DocumentTable;

class CertificationForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('first_history');

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
            'name' => 'cod_cer',
            'type' => 'number',
            'options' => array(
                'label' => 'código de certificación',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'cod_adm',
            'type' => 'number',
            'options' => array(
                'label' => 'código de la admisión',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'cod_tip_doc',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo de documento',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'class' => 'form-control',
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
                'class' => 'form-control',
                'minlength' => 4,
                'maxlength' => 20,
            ),
        ));

        $this->add(array(
            'name' => 'tit_cer',
            'type' => 'text',
            'options' => array(
                'label' => 'Título',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'Sin título',
                'maxlength' => 600,
                'rows' => 3
            ),
        ));

        $this->add(array(
            'name' => 'usu_med',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre del Médico',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'des_cer',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Detalle de la certificación',
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
                'class' => 'form-control input-sm',
                'maxlength' => 5000,
                'rows' => 3,
                'cols' => 100
            ),
        ));

        $this->add(array(
            'name' => 'fec_reg',
            'type' => 'date',
            'options' => array(
                'label' => 'Fecha',
            ),
            'attributes' => array(
                "class" => 'form-control input-sm',
                'required' => 'required',
                'value' => date("Y-m-d")
            ),
        ));

        $this->add(array(
            'name' => 'cod_usu',
            'type' => 'text',
            'options' => array(
                'label' => 'Usuario médico',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'documento, avatar, ...',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '20'
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
