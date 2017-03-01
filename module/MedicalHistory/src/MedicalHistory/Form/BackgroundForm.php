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

class BackgroundForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('antecedentes');

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
            'name' => 'cod_ant',
            'type' => 'number',
            'options' => array(
                'label' => 'código del antecedente',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'tip_ant',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo',
                'value_options' => array(
                    1 => "Médicos",
                    2 => "Quirúrgicos",
                    3 => "Transfusionables",
                    4 => "Tóxicos",
                    5 => "Inmunológicos",
                    6 => "Alérgicos",
                    7 => "Traumáticos",
                    8 => "Psiquiátricos",
                    9 => "Ginecológicos",
                    10 => "Farmacológicos",
                    11 => "Familiares",
                    12 => "Psicológicos",
                    13 => "Otros",
                ),
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'value' => '1',
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
            'name' => 'usu_reg',
            'type' => 'text',
            'options' => array(
                'label' => 'Usuario médico',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'documento, avatar, ...',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '20'
            ),
        ));

        $this->add(array(
            'name' => 'des_ant',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Descripción del antecedente',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'required' => 'required',
                'maxlength' => '5000',
                'rows' => '5',
                'cols' => '100',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-default btn-sm',
                'value' => 'Registrar'
            ),
        ));

    }
}
