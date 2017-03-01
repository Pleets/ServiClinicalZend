<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Model;

use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Acl as ZendAclAdapter;

use Zend\Permissions\Acl\Acl;

class AclAdapter
{
	private $acl;
    private $rol;

	public function __construct($controller = null)
	{
        if (is_null($controller))
        {
            $acl = new ZendAclAdapter();
        }
        else
            $acl = $controller->getServiceLocator()->get('ZendAcl');

        if (!count($acl->getRoles()))
        {
            // Definición de roles
            $superadministrador = new Role('superadministrador');
            $administrador = new Role('administrador');
            $medico = new Role('medico');
            $aux_admin = new Role('aux_admin');
            $invitado = new Role('invitado');

            // Definición de recursos
            $viewUsers = new Resource('viewUsers');
            $addUsers = new Resource('addUsers');
            $editUsers = new Resource('editUsers');
            $deleteUsers = new Resource('deleteUsers');
            $enableUsers = new Resource('enableUsers');
            $disableUsers = new Resource('disableUsers');
            $viewPermissions = new Resource('viewPermissions');
            $viewPatients = new Resource('viewPatients');
            $addPatients = new Resource('addPatients');
            $editPatients = new Resource('editPatients');
            $deletePatients = new Resource('deletePatients');
            $admissionPatients = new Resource('admissionPatients');
            $viewAdmissions = new Resource('viewAdmissions');
            $editAdmissions = new Resource('editAdmissions');
            $deleteAdmissions = new Resource('deleteAdmissions');
            $openAdmissions = new Resource('openAdmissions');
            $annulAdmissions = new Resource('annulAdmissions');
            $closeAdmissions = new Resource('closeAdmissions');
            $viewExams = new Resource('viewExams');
            $addExams = new Resource('addExams');
            $editExams = new Resource('editExams');
            $enableExams = new Resource('enableExams');
            $disableExams = new Resource('disableExams');
            $deleteExams = new Resource('deleteExams');
            $viewMedications = new Resource('viewMedications');
            $addMedications = new Resource('addMedications');
            $editMedications = new Resource('editMedications');
            $enableMedications = new Resource('enableMedications');
            $disableMedications = new Resource('disableMedications');
            $deleteMedications = new Resource('deleteMedications');
            $viewEntities = new Resource('viewEntities');
            $addEntities = new Resource('addEntities');
            $editEntities = new Resource('editEntities');
            $enableEntities = new Resource('enableEntities');
            $disableEntities = new Resource('disableEntities');
            $deleteEntities = new Resource('deleteEntities');
            $viewHistory = new Resource('viewHistory');
            $addHistory = new Resource('addHistory');
            $viewDevelopment = new Resource('viewDevelopment');

            $acl->addRole($medico)
                ->addRole($invitado)
            ;

            $acl->addResource($viewUsers)
                ->addResource($addUsers)
                ->addResource($editUsers)
                ->addResource($deleteUsers)
                ->addResource($enableUsers)
                ->addResource($disableUsers)
                ->addResource($viewPermissions)
                ->addResource($viewPatients)
                ->addResource($addPatients)
                ->addResource($editPatients)
                ->addResource($deletePatients)
                ->addResource($viewAdmissions)
                ->addResource($editAdmissions)
                ->addResource($deleteAdmissions)
                ->addResource($openAdmissions)
                ->addResource($annulAdmissions)
                ->addResource($closeAdmissions)
                ->addResource($admissionPatients)
                ->addResource($viewExams)
                ->addResource($addExams)
                ->addResource($editExams)
                ->addResource($enableExams)
                ->addResource($disableExams)
                ->addResource($deleteExams)
                ->addResource($viewMedications)
                ->addResource($addMedications)
                ->addResource($editMedications)
                ->addResource($enableMedications)
                ->addResource($disableMedications)
                ->addResource($deleteMedications)
                ->addResource($viewEntities)
                ->addResource($addEntities)
                ->addResource($editEntities)
                ->addResource($enableEntities)
                ->addResource($disableEntities)
                ->addResource($deleteEntities)
                ->addResource($viewHistory)
                ->addResource($addHistory)
                ->addResource($viewDevelopment)
            ;

            $acl->addRole($aux_admin, array('invitado'));
            $acl->addRole($administrador, array('invitado', 'aux_admin'));
            $acl->addRole($superadministrador, array('invitado', 'aux_admin', 'administrador'));

            $parents = array('invitado', 'aux_admin', 'administrador', 'superadministrador');
            $root = new Role('root');
            $acl->addRole($root, $parents);

            $acl->allow('medico', null, array(
                'viewPatients', 'viewAdmissions', 'viewExams', 'viewMedications',
                'viewEntities', 'viewHistory', 'addHistory', 'addExams', 'editExams',
                'addMedications', 'editMedications'
            ));

            $acl->allow('aux_admin', null, array(
                'viewPatients', 'viewAdmissions', 'addPatients', 'admissionPatients', 'editPatients',
                'editAdmissions', 'annulAdmissions', 'closeAdmissions', 'viewHistory'
            ));
            $acl->allow('administrador', null, array(
                'viewUsers', 'viewExams', 'viewMedications', 'viewEntities', 'viewHistory',
                'enableUsers', 'disableUsers','enableExams', 'disableExams',
                'enableMedications', 'disableMedications', 'enableEntities', 'disableEntities'
            ));
            $acl->deny('administrador', null, array(
                'admissionPatients', 'annulAdmissions', 'closeAdmissions'
            ));
            $acl->allow('superadministrador', null, array(
                'addUsers', 'viewPermissions', 'viewDevelopment', 'openAdmissions',
                'addExams', 'editExams', 'addMedications', 'editMedications', 'addEntities',
                'editEntities'
            ));
            $acl->allow('root', null, array(
                'deleteUsers', 'deletePatients', 'deleteAdmissions','editAdmissions', 'editUsers',
                'annulAdmissions', 'closeAdmissions', 'admissionPatients', 'deleteExams',
                'deleteMedications', 'deleteEntities'
            ));
        }

		$this->acl = $acl;
	}

	public function getAcl()
	{
		return $this->acl;
	}

    public function parseRol($cod_per)
    {
        $dataBaseRoles = array(
            1 => 'superadministrador',
            2 => 'administrador',
            3 => 'aux_admin',
            4 => 'medico',
            5 => 'invitado',
            6 => 'root',
        );

        foreach ($dataBaseRoles as $key => $value) {
            if ($key == $cod_per) {
                $this->rol = $dataBaseRoles[$key];
                return $this;
            }
        }

        return false;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function isAllowed($resource)
    {
        return $this->acl->isAllowed($this->rol, null, $resource);
    }

}
