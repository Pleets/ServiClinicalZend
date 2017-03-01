<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Settings\Form\ExamForm;
use Settings\Model\Entity\Exam;
use Settings\Form\MedicationForm;
use Settings\Model\Entity\Medication;
use Settings\Form\EntityForm;
use Settings\Model\Entity\Entity;
use Auth\Model\AclAdapter;

class ManageController extends AbstractActionController
{
    private $examTable;
    private $entityTable;
    private $medicationTable;
    private $userTable;

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

    private function getExamTable()
    {
        if (!$this->examTable) {
            $sm = $this->getServiceLocator();
            $this->examTable = $sm->get('Settings\Model\Entity\ExamsTable');
        }
        return $this->examTable;
    }

    private function getMedicationTable()
    {
        if (!$this->medicationTable) {
            $sm = $this->getServiceLocator();
            $this->medicationTable = $sm->get('Settings\Model\Entity\MedicationsTable');
        }
        return $this->medicationTable;
    }

    private function getEntityTable()
    {
        if (!$this->entityTable) {
            $sm = $this->getServiceLocator();
            $this->entityTable = $sm->get('Settings\Model\Entity\EntitiesTable');
        }
        return $this->entityTable;
    }

	public function indexAction()
	{
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        $acl = $this->configureAcl();

        try {
            if (!$acl->isAllowed("viewExams") && !$acl->isAllowed("viewMedications") && !$acl->isAllowed("entities"))
                throw new \Exception("No tienes permiso para ver este módulo");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

		return new ViewModel(array(
			'exams' => $this->getExamTable()->search(),
            'acl' => $this->configureAcl(),
            'num_exams' => $this->getExamTable()->countExams(),
            'num_medications' => $this->getMedicationTable()->countMedications(),
            'num_entities' => $this->getEntityTable()->countEntities(),
        ));
	}

    public function viewExamsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewExams"))
                throw new \Exception("No tienes permiso para ver exámenes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $exam = $this->getRequest()->getPost("request");
        $view = new ViewModel(array(
            'needle' => $exam,
            'exams' => $this->getExamTable()->search($exam),
            'empty' => ( bool ) !$this->getExamTable()->hasExams(),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
            'acl' => $this->configureAcl()
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function viewMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewMedications"))
                throw new \Exception("No tienes permiso para ver medicamentos");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $medication = $this->getRequest()->getPost("request");
        $view = new ViewModel(array(
            'needle' => $medication,
            'empty' => ( bool ) !$this->getMedicationTable()->hasMedications(),
            'medications' => $this->getMedicationTable()->search($medication),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
            'acl' => $this->configureAcl()
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function viewEntitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewEntities"))
                throw new \Exception("No tienes permiso para ver entidades");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $entity = $this->getRequest()->getPost("request");
        $view = new ViewModel(array(
            'needle' => $entity,
            'empty' => ( bool ) !$this->getEntityTable()->hasEntities(),
            'entities' => $this->getEntityTable()->search($entity),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
            'acl' => $this->configureAcl()
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function addExamAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("addExams"))
                throw new \Exception("No tienes permiso para agregar exámenes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form = new ExamForm();
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $exams = new Exam();
            $form->setInputFilter($exams->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $exams->exchangeArray($form->getData());
                try {
                    $this->getExamTable()->addExam($exams);
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

    public function addMedicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("addMedications"))
                throw new \Exception("No tienes permiso para agregar medicamentos");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form = new MedicationForm();
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $medication = new Medication();
            $form->setValidationGroup('nom_med', 'nom_gen', 'con_med', 'pre_med', 'est_med');
            $form->setInputFilter($medication->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $medication->exchangeArray($form->getData());
                try {
                    $this->getMedicationTable()->addMedication($medication);
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

    public function addEntityAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("addEntities"))
                throw new \Exception("No tienes permiso para agregar entidades");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form = new EntityForm();
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $entity = new Entity();
            $form->setValidationGroup('cod_ent', 'nom_ent', 'dir_ent', 'est_ent');
            $form->setInputFilter($entity->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $entity->exchangeArray($form->getData());
                try {
                    $this->getEntityTable()->addEntity($entity);
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

    public function editExamAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editExams"))
                throw new \Exception("No tienes permiso para editar exámenes");
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
            $id = $id["cod_exa"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('settings', array(
                'action' => 'add-exam'
            ));

        try {
            $exam = $this->getExamTable()->getExam($id);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new ExamForm();
        $form->bind($exam);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup('cod_exa', 'nom_exa', 'tip_exa');
            $form->setInputFilter($exam->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $this->getExamTable()->updateExam($exam);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    return array(
                        'id' => $id,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
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

    public function editMedicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editMedications"))
                throw new \Exception("No tienes permiso para editar medicamentos");
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
            $id = $id["cod_med"];
        }
        else
            $id = (int) $this->params()->fromRoute('id', 0);

        if ($id == 0)
            return $this->redirect()->toRoute('settings', array(
                'action' => 'add-medication'
            ));

        try {
            $medication = $this->getMedicationTable()->getMedication($id);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new MedicationForm();
        $form->bind($medication);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup('cod_med', 'nom_med', 'nom_gen', 'con_med', 'pre_med');
            $form->setInputFilter($medication->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $this->getMedicationTable()->updateMedication($medication);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    return array(
                        'id' => $id,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
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

    public function editEntityAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editEntities"))
                throw new \Exception("No tienes permiso para editar entidades");
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
            $id = $id["cod_ent"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', '');

        if (empty($id))
            return $this->redirect()->toRoute('settings', array(
                'action' => 'add-entity'
            ));

        try {
            $entity = $this->getEntityTable()->getEntity($id);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new EntityForm();
        $form->bind($entity);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup('cod_ent', 'nom_ent', 'dir_ent');
            $form->setInputFilter($entity->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $this->getEntityTable()->updateEntity($entity);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    return array(
                        'id' => $id,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
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

    public function deleteExamsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deleteExams"))
                throw new \Exception("No tienes permiso para eliminar exámenes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $exams = $this->getRequest()->getPost("request");
            foreach ($exams as $exam) {
                $this->getExamTable()->deleteExam($exam);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $exams ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        if (empty($id))
            return $this->redirect()->toRoute('settings');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (string) $request->getPost('id');
                $this->getExamTable()->deleteExam($id);
            }

            return $this->redirect()->toRoute('settings');
        }

        return array(
            'id'    => $id,
            'exam' => $this->getExamTable()->getExam($id),
        );
    }

    public function deleteMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deleteMedications"))
                throw new \Exception("No tienes permiso para eliminar medicamentos");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $medications = $this->getRequest()->getPost("request");
            foreach ($medications as $medication) {
                $this->getMedicationTable()->deleteMedication($medication);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $medications ) ));
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id == 0)
            return $this->redirect()->toRoute('settings');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (int) $request->getPost('id');
                $this->getMedicationTable()->deleteMedication($id);
            }

            return $this->redirect()->toRoute('settings');
        }

        return array(
            'id'    => $id,
            'medication' => $this->getMedicationTable()->getMedication($id),
        );
    }

    public function deleteEntitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deleteEntities"))
                throw new \Exception("No tienes permiso para eliminar entidades");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $entities = $this->getRequest()->getPost("request");
            foreach ($entities as $entity) {
                $this->getEntityTable()->deleteEntity($entity);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $entities ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', '');
        if (empty($id))
            return $this->redirect()->toRoute('settings');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (string) $request->getPost('id');
                $this->getEntityTable()->deleteEntity($id);
            }

            return $this->redirect()->toRoute('settings');
        }

        return array(
            'id'    => $id,
            'entity' => $this->getEntityTable()->getEntity($id),
        );
    }

    public function disableExamsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("disableExams"))
                throw new \Exception("No tienes permiso para deshabilitar exámenes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $exams = $this->getRequest()->getPost("request");
            foreach ($exams as $exam) {
                $this->getExamTable()->disableExam($exam);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $exams ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getExamTable()->disableExam($id);
        return $this->redirect()->toRoute('settings');
    }

    public function disableMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("disableMedications"))
                throw new \Exception("No tienes permiso para deshabilitar medicamentos");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $medications = $this->getRequest()->getPost("request");
            foreach ($medications as $medication) {
                $this->getMedicationTable()->disableMedication($medication);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $medications ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getMedicationTable()->disableMedication($id);
        return $this->redirect()->toRoute('settings');
    }

    public function disableEntitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("disableEntities"))
                throw new \Exception("No tienes permiso para deshabilitar entidades");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $entities = $this->getRequest()->getPost("request");
            foreach ($entities as $entity) {
                $this->getEntityTable()->disableEntity($entity);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $entities ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getEntityTable()->disableEntity($id);
        return $this->redirect()->toRoute('settings');
    }

    public function enableExamsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("enableExams"))
                throw new \Exception("No tienes permiso para habilitar exámenes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $exams = $this->getRequest()->getPost("request");
            foreach ($exams as $exam) {
                $this->getExamTable()->enableExam($exam);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $exams ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getExamTable()->enableExam($id);
        return $this->redirect()->toRoute('settings');
    }

    public function enableMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("enableMedications"))
                throw new \Exception("No tienes permiso para habilitar medicamentos");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $medications = $this->getRequest()->getPost("request");
            foreach ($medications as $medication) {
                $this->getMedicationTable()->enableMedication($medication);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $medications ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getMedicationTable()->enableMedication($id);
        return $this->redirect()->toRoute('settings');
    }

    public function enableEntitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("enableEntities"))
                throw new \Exception("No tienes permiso para habilitar entidades");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $entities = $this->getRequest()->getPost("request");
            foreach ($entities as $entity) {
                $this->getEntityTable()->enableEntity($entity);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $entities ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', 0);
        $this->getExamTable()->enableExam($id);
        return $this->redirect()->toRoute('settings');
    }

}
