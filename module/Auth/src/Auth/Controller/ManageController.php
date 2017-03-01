<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\ViewModel;
use Auth\Model\Entity\User;
use Auth\Model\Entity\LogIngress;
use Auth\Model\AclAdapter;
use Auth\Form\UserForm;

class ManageController extends AbstractActionController
{
	private $userTable;
    private $logIngressTable;
	private $acl;

    private function authentication()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {

            $terminal = ($xmlHttpRequest = $this->getRequest()->isXmlHttpRequest()) ? "xmlHttpRequest" : "attemp";

            return $this->redirect()->toRoute('auth',
                array('action' => 'login', 'id' => $terminal)
            );
        }
    }

    private function forceAuthentication()
    {
        $auth = new AuthenticationService();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        if (!$auth->hasIdentity() && $xmlHttpRequest)
            exit;
    }

    private function authenticate()
    {
        $this->authentication();
        $this->forceAuthentication();
    }

    private function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Auth\Model\Entity\UserTable');
        }
        return $this->userTable;
    }

    private function getIdentity()
    {
        $auth = new AuthenticationService();
        return $auth->getIdentity();
    }

    private function configureAcl()
    {
        $acl = new AclAdapter($this);
        $acl->parseRol($this->getUserTable()->getPermission($this->getIdentity()->cod_usu));
        $this->acl = $acl;
        return $acl;
    }

    private function isAllow($resource)
    {
        $acl = new AclAdapter($this);
        $acl->parseRol($this->getUserTable()->getPermission($this->getIdentity()->cod_usu));
        return $acl->isAllowed($resource);
    }

    private function getLogIngressTable()
    {
        if (!$this->logIngressTable) {
            $sm = $this->getServiceLocator();
            $this->logIngressTable = $sm->get('Auth\Model\Entity\LogIngressTable');
        }
        return $this->logIngressTable;
    }

	public function indexAction()
	{
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        $acl = $this->configureAcl();

        try {
            if (!$acl->isAllowed("viewUsers") || !$acl->isAllowed("viewPermissions"))
                throw new \Exception("No tienes permiso para ver este módulo");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        return new ViewModel(array(
            'users' => $this->getUserTable()->search(),
            'num_users' => $this->getUserTable()->countUsers(),
            'num_active_users' => $this->getUserTable()->countActiveUsers(),
            'num_inactive_users' => $this->getUserTable()->countInactiveUsers(),
            'last_user_registered' => $this->getUserTable()->lastUserRegistered(),
            'viewUsers' => $this->isAllow("viewUsers"),
            'viewUsersPermissions' => $this->isAllow("viewPermissions"),
            'acl' => $this->configureAcl()
        ));
    }

    public function createDefaultUserAction()
    {
        if (count($this->getUserTable()->search()))
            return $this->redirect()->toRoute('auth');

        $model_data = array();

        $users = new User();
        $bcrypt = new Bcrypt();

        $data = array(
            "cod_usu" => 'admin',
            "nom_usu" => 'Administrador',
            "cod_per" => 1,
            "fec_reg_usu" => date("Y:d:m H:i:s"),
            "est_usu" => 1,
            "pas_usu" => $bcrypt->create("admin"),
        );

        $users->exchangeArray($data);

        try {
            $this->getUserTable()->addUser($users);
            $model_data['Success'] = true;
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);
            return $view;
        }

        $view = new ViewModel($model_data);
        return $view;
    }

    public function changePasswordAction()
    {
        if (count(!$this->getUserTable()->search()->toArray()) < 1)
            return $this->redirect()->toRoute('auth');

        $model_data = array();

        $request = $this->getRequest();

        $auth = new AuthenticationService();

        if (!$auth->hasIdentity())
            return $this->redirect()->toRoute('home');

        $form = new UserForm($this);
        $form->get('submit')->setValue('Cambiar');
        $model_data["form"] = $form;

        if ($request->isPost())
        {
            $users = new User();
            $bcrypt = new Bcrypt();

            $cod_usu = $auth->getIdentity()->cod_usu;

            $form->setValidationGroup('pas_usu');
            $form->setInputFilter($users->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $users->exchangeArray($form->getData());
                $bcrypt = new Bcrypt();
                $users->pas_usu = $bcrypt->create($users->pas_usu);
                $users->cod_usu = $cod_usu;

                try {
                    $this->getUserTable()->changePassword($users);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);
                    if ($xmlHttpRequest)
                        $view->setTerminal(true);
                    return $view;
                }
            }
            else
                $model_data['form'] = $form;
        }

        $view = new ViewModel($model_data);
        return $view;
    }

    public function loginAction()
    {
        $data = array();

        $authCode = null;
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }
        else
        {
            $data = $this->request->getPost();

            if (!is_null($data->cod_usu) && !is_null($data->pas_usu))
            {
                $DbAuthAdapter = $this->getServiceLocator()->get('AuthAdapter');

                $bcrypt = new Bcrypt();

                $search = $this->getUserTable()->isUser($data->cod_usu);
                $password = $data->pas_usu;

                if ($search)
                {
                    $user = $this->getUserTable()->getUser($data->cod_usu);
                    $check = $bcrypt->verify($data->pas_usu, $user->pas_usu);

                    $password = ($check) ? $user->pas_usu: $data->pas_usu;
                }

                $DbAuthAdapter
                    ->setIdentity($data->cod_usu)
                    ->setCredential($password)
                ;

                $result = $auth->authenticate($DbAuthAdapter);
                $authCode = $result->getCode();

                if ($result->isValid())
                {
                    $storage = $auth->getStorage();
                    $storage->write($DbAuthAdapter->getResultRowObject('cod_usu', 'nom_usu', 'cod_per'));

	                function getRealIP() {
	                    if (!empty($_SERVER['HTTP_CLIENT_IP']))
	                        return $_SERVER['HTTP_CLIENT_IP'];

	                    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	                        return $_SERVER['HTTP_X_FORWARDED_FOR'];

	                    return $_SERVER['REMOTE_ADDR'];
	                }

                    $ingress = new LogIngress();

	                $_auth = new AuthenticationService();
	                $_user = $_auth->getIdentity()->cod_usu;

                    $data = array(
                    	'ip_address' => getRealIP(),
                    	'cod_usu' => $_user,
                    );

                    $ingress->exchangeArray($data);

                    $this->getLogIngressTable()->addIngress($ingress);

                }
            }
        }

        $form = new userForm($this);
        $action = $this->getRequest()->getBaseUrl();

        $data["formLogin"] = $form;
        $data["formAction"] = $action;
        $data["authCode"] = $authCode;

        $attemp = $this->params()->fromRoute('id', 'validate');

        if ($attemp == 'xmlHttpRequest' || $attemp == 'attemp')
            $data["authCode"] = Result::FAILURE_IDENTITY_AMBIGUOUS;

        if ($attemp == 'xmlHttpRequest')
            $view->setTerminal(true);

        $data['users'] = $this->getUserTable()->search();

        $view = new ViewModel($data);
        return $view;
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity())
            $auth->clearIdentity();

        return $this->redirect()->toRoute('auth', array(
            'action' => 'login'
        ));
    }

    public function meAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $auth = new AuthenticationService();
        $myUser = $this->getUserTable()->getUser($auth->getIdentity()->cod_usu);

        $model_data = array();
        $model_data["me"] = $myUser;

        $acl = new AclAdapter($this);
        $acl = $acl->getAcl();
        $resources = $acl->getResources();
        $model_data["resources"] = $resources;

        // Activity
        $num_admissions = $this->getUserTable()->countAdmissions($auth->getIdentity()->cod_usu);
        $model_data["num_admissions"] = $num_admissions;
        $num_patients = $this->getUserTable()->countPatients($auth->getIdentity()->cod_usu);
        $model_data["num_patients"] = $num_patients;
        $num_income = $this->getUserTable()->countIncome($auth->getIdentity()->cod_usu);
        $model_data["num_income"] = $num_income;
        $num_entries = $this->getUserTable()->countEntries($auth->getIdentity()->cod_usu);
        $model_data["num_entries"] = $num_entries;
        $num_certifications = $this->getUserTable()->countCertifications($auth->getIdentity()->cod_usu);
        $model_data["num_certifications"] = $num_certifications;

        $model_data["acl"] = $this->configureAcl();
        $model_data["controller"] = $this;

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function viewUsersAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewUsers"))
                throw new \Exception("No tienes permiso para ver usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $user = $this->getRequest()->getPost("request");
        $view = new ViewModel(array(
            'needle' => $user,
            'empty' => ( bool ) !$this->getUserTable()->hasUsers(),
            'users' => $this->getUserTable()->search($user),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => $simulateXmlHttpRequest,
            'acl' => $this->configureAcl()
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function addAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("addUsers"))
                throw new \Exception("No tienes permiso para registrar usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form = new UserForm($this);
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $users = new User();
            $form->setInputFilter($users->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $users->exchangeArray($form->getData());
                $bcrypt = new Bcrypt();
                $users->pas_usu = $bcrypt->create($users->pas_usu);
                try {
                    $this->getUserTable()->addUser($users);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);
                    if ($xmlHttpRequest)
                        $view->setTerminal(true);
                    return $view;
                }
            }
            else
                $model_data['form'] = $form;
        }

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
	}

    public function editAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editUsers"))
                throw new \Exception("No tienes permiso para editar usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $id = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit") {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_usu"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', '');

        if (empty($id))
            return $this->redirect()->toRoute('auth', array(
                'action' => 'add'
            ));

        try {
            $user = $this->getUserTable()->getUser($id);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new UserForm($this);
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();
        $userO = new User();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup('cod_usu', 'nom_usu', 'cod_per');
            $form->setInputFilter($userO->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $userO->exchangeArray($form->getData());
                    $this->getUserTable()->updateUser($userO);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    return array(
                        'id' => $id,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
                        'xmlHttpRequest' => $xmlHttpRequest,
                    );
                }
            }
        }

        $model_data['id'] = $id;
        $model_data['form'] = $form;
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
	}

	public function deleteAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deleteUsers"))
                throw new \Exception("No tienes permiso para eliminar usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $users = $this->getRequest()->getPost("request");
            foreach ($users as $value) {
                $this->getUserTable()->deleteUser($value);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $users ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', '');
        if (empty($id))
            return $this->redirect()->toRoute('auth');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (string) $request->getPost('id');
                $this->getUserTable()->deleteUser($id);
            }

            return $this->redirect()->toRoute('auth', array('action' => 'view-users'));
        }

        return array(
            'id'   => $id,
            'user' => $this->getUserTable()->getUser($id),
            'xmlHttpRequest' => $xmlHttpRequest
        );
	}

    public function disableAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("disableUsers"))
                throw new \Exception("No tienes permiso para inactivar usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $users = $this->getRequest()->getPost("request");
            foreach ($users as $value) {
                $this->getUserTable()->disableUser($value);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $users ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getUserTable()->disableUser($id);
        return $this->redirect()->toRoute('auth', array('action' => 'view-users'));
    }

    public function enableAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("enableUsers"))
                throw new \Exception("No tienes permiso para activar usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $users = $this->getRequest()->getPost("request");
            foreach ($users as $value) {
                $this->getUserTable()->enableUser($value);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $users ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getUserTable()->enableUser($id);
        return $this->redirect()->toRoute('auth', array('action' => 'view-users'));
    }

    public function parseResource($needle)
    {
        $resources = array(
            "viewUsers" => "Ver usuarios",
            "addUsers" => "Registrar usuarios",
            "editUsers" => "Editar usuarios",
            "deleteUsers" => "Eliminar usuarios",
            "enableUsers" => "Activar usuarios",
            "disableUsers" => "Bloquear usuarios",
            "viewPermissions" => "Ver permisos",
            "viewPatients" => "Ver pacientes",
            "addPatients" => "Registrar pacientes",
            "editPatients" => "Editar pacientes",
            "deletePatients" => "Eliminar pacientes",
            "viewAdmissions" => "Ver admisiones",
            "editAdmissions" => "Editar admisiones",
            "deleteAdmissions" => "Eliminar admisiones",
            "openAdmissions" => "Abrir admisiones",
            "annulAdmissions" => "Anular admisiones",
            "closeAdmissions" => "Cerrar admisiones",
            "admissionPatients" => "Admisionar pacientes",
            "viewExams" => "Ver exámenes",
            "addExams" => "Registrar exámenes",
            "editExams" => "Editar exámenes",
            "enableExams" => "Activar exámenes",
            "disableExams" => "Bloquear exámenes",
            "deleteExams" => "Eliminar exámenes",
            "viewMedications" => "Ver medicamentos",
            "addMedications" => "Registrar medicamentos",
            "editMedications" => "Editar medicamentos",
            "enableMedications" => "Activar medicamentos",
            "disableMedications" => "Bloquear medicamentos",
            "deleteMedications" => "Eliminar medicamentos",
            "viewEntities" => "Ver entidades",
            "addEntities" => "Registrar entidades",
            "editEntities" => "Editar entidades",
            "enableEntities" => "Activar entidades",
            "disableEntities" => "Bloquear entidades",
            "deleteEntities" => "Eliminar entidades",
            "viewHistory" => "Ver historia clínica",
            "addHistory" => "Registrar historia clínica",
            "viewDevelopment" => "Ver desarrollo",
        );

        foreach ($resources as $key => $value) {
            if ($needle == $key)
                return $value;
        }
        return $needle;
    }

    public function permissionsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $acl = new AclAdapter($this);
        $acl = $acl->getAcl();

        try {
            if (!$this->isAllow("viewPermissions"))
                throw new \Exception("No tienes permiso para ver los permisos de los usuarios");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage()));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $roles = $acl->getRoles();
        $resources = $acl->getResources();

        $model_data = array(
            'acl' => $acl,
            'roles' => $roles,
            'resources' => $resources,
            'xmlHttpRequest' => $xmlHttpRequest,
            'controller' => $this
        );

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

}
