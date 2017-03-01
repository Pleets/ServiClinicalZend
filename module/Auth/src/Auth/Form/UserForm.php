<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Db\ResultSet\ResultSet;
use Auth\Model\Entity\Profile;
use Auth\Model\Entity\ProfileTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class UserForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('users');

        $dbAdapter = $controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $tableGateway = new TableGateway('hcneuro_perfil', $dbAdapter);

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Profile());

        $profileTable = new ProfileTable($tableGateway, $dbAdapter, null, $resultSetPrototype);

        $result = $profileTable->fetchAll()->toArray();

        $profiles = array();
        foreach ($result as $profile) {
            $profiles[$profile["cod_per"]] = $profile["nom_per"];
        }

        $this->add(array(
            'name' => 'cod_usu',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre de usuario',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'documento, avatar, ...',
                'autofocus' => 'autofocus',
                'required' => 'required',
                'minlength' => '5',
                'maxlength' => '20'
            ),
        ));

        $this->add(array(
            'name' => 'nom_usu',
            'type' => 'text',
            'options' => array(
                'label' => 'Nombre',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'nombre completo',
                'required' => 'required',
                'minlength' => '10',
                'maxlength' => '50',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name' => 'cod_per',
            'type' => 'select',
            'options' => array(
                'label' => 'Permiso',
                'value_options' => $profiles,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'value' => '3',
            ),
        ));

        $this->add(array(
            'name' => 'est_usu',
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
            'name' => 'fir_usu',
            'type' => 'file',
            'options' => array(
                'label' => 'Firma',
            ),
        ));

        $this->add(array(
            'name' => 'pas_usu',
            'type' => 'password',
            'options' => array(
                'label' => 'Contraseña',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'placeholder' => 'password',
                'required' => 'required',
                'minlength' => '8',
                'maxlength' => '50',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-default btn-sm',
                'value' => 'Iniciar Sesión'
            ),
        ));

    }
}
