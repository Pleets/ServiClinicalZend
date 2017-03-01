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
use Admissions\Model\Entity\Area;
use Admissions\Model\Entity\AreasTable;
use Settings\Model\Entity\Entity;
use Settings\Model\Entity\EntitiesTable;
use Auth\Model\Entity\User;
use Auth\Model\Entity\UserTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class AdmissionForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('patients');

        $dbAdapter = $controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new ResultSet();

        // Document types

        $tableGateway = new TableGateway('hcneuro_tipodoc', $dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Document());
        $documentTable = new DocumentTable($tableGateway, $dbAdapter, null, $resultSetPrototype);
        $result = $documentTable->fetchAll()->toArray();

        $documents = array();
        foreach ($result as $document) {
            $documents[$document["cod_tip_doc"]] = $document["nom_tip_doc"];
        }

        // Areas

        $tableGateway = new TableGateway('hcneuro_areas', $dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Document());
        $areaTable = new AreasTable($tableGateway, $dbAdapter, null, $resultSetPrototype);
        $result = $areaTable->fetchAll()->toArray();

        $areas = array();
        foreach ($result as $area) {
            $areas[$area["cod_are"]] = $area["nom_are"];
        }

        // Entities

        $tableGateway = new TableGateway('hcneuro_entidad', $dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Entity());
        $areaTable = new EntitiesTable($tableGateway, $dbAdapter, null, $resultSetPrototype);
        $result = $areaTable->fetchAll()->toArray();

        $entities = array();
        foreach ($result as $entity) {
            $entities[$entity["cod_ent"]] = $entity["nom_ent"];
        }

        // Doctors

        $tableGateway = new TableGateway('hcneuro_usuarios', $dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new User());
        $userTable = new UserTable($tableGateway, $dbAdapter, null, $resultSetPrototype);
        $result = $userTable->fetchDoctors()->toArray();

        $doctors = array();
        foreach ($result as $doctor) {
            $doctors[$doctor["cod_usu"]] = $doctor["nom_usu"];
        }

        $this->add(array(
            'name' => 'cod_adm',
            'type' => 'number',
            'options' => array(
                'label' => 'Código de la admisión',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'código',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'min' => '1',
                'max' => '99999999999999999999',
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
            ),
            'attributes' => array(
                'placeholder' => 'documento',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 20,
            ),
        ));

        $this->add(array(
            'name' => 'cod_are',
            'type' => 'select',
            'options' => array(
                'label' => 'Área',
                'value_options' => $areas,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'cod_ent',
            'type' => 'select',
            'options' => array(
                'label' => 'Entidad',
                'value_options' => $entities,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'obs_adm',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Observaciones',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '2000',
                'rows' => '6',
            ),
        ));

        $this->add(array(
            'name' => 'cod_usu_med',
            'type' => 'select',
            'options' => array(
                'label' => 'Médico',
                'value_options' => $doctors,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
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
