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

class LiquidosForm extends Form
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
            'name' => 'material_estudio',
            'type' => 'text',
            'options' => array(
                'label' => 'Material estudio',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => 'Material estudio',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 150,
            ),
        ));
        
         $this->add(array(
            'name' => 'diagnostico_clinico',
            'type' => 'text',
            'options' => array(
                'label' => 'Diagnostico clinico',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => 'Dianostico clinico',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 150,
            ),
        ));

        $this->add(array(
            'name' => 'descripcion_macroscopica',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Descripcion Macroscopica',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '7',
                'cols' => '200'
            ),
        ));
        
        $this->add(array(
            'name' => 'descripcion_microscopica',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Descripcion Microscopica',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '7',
                'cols' => '200'
            ),
        ));
        
         $this->add(array(
            'name' => 'diagnostico',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Diagnostico',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '6',
                'cols' => '200'
            ),
        ));
        
        
         $this->add(array(
            'name' => 'nota',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Nota',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '5',
                'cols' => '200'
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Guardar'
            ),
        ));

    }
}
