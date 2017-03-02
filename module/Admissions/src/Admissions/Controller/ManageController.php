<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Admissions\Model\Entity\Patient;
use Admissions\Model\Entity\Admission;
use Admissions\Form\PatientForm;
use Admissions\Form\AdmissionForm;
use MedicalHistory\Model\Entity\HistoryTable;
use Auth\Model\AclAdapter;

class ManageController extends AbstractActionController
{
	private $patientTable;
    private $admissionTable;
    private $userTable;
	private $dbAdapter;

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

    private function getDbAdapter()
    {
        if (!$this->dbAdapter) {
            $sm = $this->getServiceLocator();
            $this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->dbAdapter;
    }

    private function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Admissions\Model\Entity\PatientTable');
        }
        return $this->patientTable;
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

    private function getAdmissionTable()
    {
        if (!$this->admissionTable) {
            $sm = $this->getServiceLocator();
            $this->admissionTable = $sm->get('Admissions\Model\Entity\AdmissionTable');
        }
        return $this->admissionTable;
    }

	public function indexAction()
	{
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        $acl = $this->configureAcl();

        try {
            if (!$acl->isAllowed("viewAdmissions") && !$acl->isAllowed("viewPatients"))
                throw new \Exception("No tienes permiso para ver este módulo");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        return new ViewModel(array(
        	'form' => new PatientForm($this),
            'patients' => $this->getPatientTable()->search(),
            'empty' => ( bool ) !$this->getPatientTable()->hasPatients(),
            'acl' => $this->configureAcl(),
            'xmlHttpRequest' => $xmlHttpRequest,
            'num_admissions' => $this->getAdmissionTable()->countAdmissions(),
            'num_patients' => $this->getPatientTable()->countPatients(),
            'last_admission_registered' => $this->getAdmissionTable()->lastAdmissionRegistered(),
            'last_patient_registered' => $this->getPatientTable()->lastPatientRegistered(),
        ));
    }

    public function viewPatientsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewPatients"))
                throw new \Exception("No tienes permiso para ver pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $patient = $this->getRequest()->getPost("request");
        $view = new ViewModel(array(
            'needle' => $patient,
            'patients' => $this->getPatientTable()->search($patient),
            'empty' => ( bool ) !$this->getPatientTable()->hasPatients(),
            'history' => $history,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
            'acl' => $this->configureAcl()
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }

    public function addPatientAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $form = new PatientForm($this);
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        try {
            if (!$this->isAllow("addPatients"))
                throw new \Exception("No tienes permiso para registrar pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $patients = new Patient();
            $form->setValidationGroup(
                'cod_tip_doc', 'num_doc_pac', 'pri_nom_pac', 'seg_nom_pac', 'pri_ape_pac',
                'seg_ape_pac', 'fec_nac_pac', 'sexo_pac', 'dir_pac', 'num_tel_pac', 'num_cel_pac'
            );
            $form->setInputFilter($patients->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $patients->exchangeArray($form->getData());
                try {
                    $this->getPatientTable()->addPatient($patients);
                    $model_data['Success'] = true;
                    $model_data['id'] = $patients->num_doc_pac;
                    $model_data['type'] = $patients->cod_tip_doc;
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

    public function editPatientAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editPatients"))
                throw new \Exception("No tienes permiso para editar pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get") {
            $id = $this->getRequest()->getPost('id');
            $type = $this->getRequest()->getPost('type');
        }
        elseif ($xmlHttpRequest && $action == "edit") {
            $id = $this->getRequest()->getPost('request');
            $id = $id["num_doc_pac"];
            $type = $this->getRequest()->getPost('request');
            $type = $type["cod_tip_doc"];
        }
        else {
            $id = (string) $this->params()->fromRoute('id', "");
            $type = (string) $this->params()->fromRoute('type', 0);
        }

        if (empty($id) || $type == 0)
            return $this->redirect()->toRoute('admissions', array(
                'action' => 'add-patient'
            ));

        try {
            $patient = $this->getPatientTable()->getPatient($id, $type);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new PatientForm($this);
        $form->bind($patient);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup(
                'cod_tip_doc', 'num_doc_pac', 'pri_nom_pac', 'seg_nom_pac', 'pri_ape_pac',
                'seg_ape_pac', 'fec_nac_pac', 'sexo_pac', 'dir_pac', 'num_tel_pac', 'num_cel_pac'
            );
            $form->setInputFilter($patient->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $this->getPatientTable()->updatePatient($patient);
                    $model_data['Success'] = true;
                }
                catch (\Exception $e) {
                    return array(
                        'id' => $id,
                        'type' => $type,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
                    );
                }
            }
        }

        $model_data['id'] = $id;
        $model_data['type'] = $type;
        $model_data['form'] = $form;
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
	}

    public function editAdmissionAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("editAdmissions"))
                throw new \Exception("No tienes permiso editar admisiones");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get") {
            $id = $this->getRequest()->getPost('request');
        }
        elseif ($xmlHttpRequest && $action == "edit") {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_adm"];
        }
        else {
            $id = (string) $this->params()->fromRoute('id', "");
        }

        if (empty($id))
            return $this->redirect()->toRoute('admissions', array(
                'action' => 'add-admission'
            ));

        try {
            $admission = $this->getAdmissionTable()->getAdmission($id);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form  = new AdmissionForm($this);
        $form->bind($admission);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" )) {
            $form->setValidationGroup(
                'cod_adm', 'cod_tip_doc', 'num_doc_pac',
                'cod_are', 'cod_ent', 'obs_adm', 'cod_usu_med'
            );
            $form->setInputFilter($admission->getInputFilter());
            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid()) {
                try {
                    $this->getAdmissionTable()->updateAdmission($admission);
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

	public function deletePatientsAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deletePatients"))
                throw new \Exception("No tienes permiso eliminar pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $id_array = $this->getRequest()->getPost("id");
            $type_array = $this->getRequest()->getPost("type");
            $data = array_combine($id_array, $type_array);
            foreach ($data as $id => $type) {
                $this->getPatientTable()->deletePatient($id, $type);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $data ) ));
            return $response;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', 0);

        if (empty($id) || $type == 0)
            return $this->redirect()->toRoute('admissions');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (string) $request->getPost('id');
                $type = (string) $request->getPost('type');

                $this->getPatientTable()->deletePatient($id, $type);
            }

            return $this->redirect()->toRoute('admissions');
        }

        return array(
            'id'    => $id,
            'type'    => $type,
            'patient' => $this->getPatientTable()->getPatient($id, $type),
        );
	}

    public function searchDiagnosticoAction()
    {

        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $model_data['id_input'] = $_POST['id_input'];



        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }

    public function searchCupsAction()
    {

        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $model_data['id_input'] = $_POST['id_input'];



        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }



    public function searchDiagnosticoaddAction()
    {


        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $valor_busqueda=$_POST['valor_busqueda'];


        $id_input=$_POST['id_input'];


        $model_data['diagnosticos']=$history->getCie10($valor_busqueda)->toArray();

        $model_data['valor_busqueda'] = $_POST['valor_busqueda'];
        $model_data['id_input'] = $_POST['id_input'];

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }


    public function searchCupsaddAction()
    {


        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $valor_busqueda=$_POST['valor_busqueda_cups'];


        $id_input=$_POST['id_input'];


        $model_data['cups']=$history->getCups($valor_busqueda)->toArray();

        $model_data['valor_busqueda_cups'] = $_POST['valor_busqueda_cups'];
        $model_data['id_input'] = $_POST['id_input'];

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }

    public function addRiaAction()
    {

        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $num_ing=$_POST['num_ing'];

        $model_data['datos_paciente']=$history->getDatosIngreso($num_ing, $_POST['cod_tip_his'])->toArray();

        $datos_historia=$model_data['datos_paciente'][0];

        if($_POST['cod_tip_his']==6)
        {

           $model_data['datos_ria_registrado']=$history->getDatosRia($datos_historia['tipo_documento'], $datos_historia['documento'], $datos_historia['numero_admision'], $datos_historia['numero_folio']);

            $model_data['flag_registro']=false;

            $model_data['datos_cups_ria_registrado']=array();

            if($model_data['datos_ria_registrado'])
            {
                $model_data['flag_registro']=true;

                $datos_ria=$model_data['datos_ria_registrado'];


                $model_data['datos_cups_ria_registrado']=$history->getDatosCupsRia($datos_ria['id'])->toArray();

            }


            $model_data['datos_ria']=$history->getDatosHistoriaPatoLiq($datos_historia['tipo_documento'], $datos_historia['documento'], $datos_historia['numero_folio'])->toArray();
        }
        else
        {

            $model_data['datos_ria_registrado']=$history->getDatosRia($datos_historia['tipo_documento'], $datos_historia['documento'], $datos_historia['numero_admision'], $datos_historia['numero_folio']);

            $model_data['flag_registro']=false;

            $model_data['datos_cups_ria_registrado']=array();

            if($model_data['datos_ria_registrado'])
            {
                $model_data['flag_registro']=true;

                $datos_ria=$model_data['datos_ria_registrado'];


                $model_data['datos_cups_ria_registrado']=$history->getDatosCupsRia($datos_ria['id'])->toArray();

            }




            $model_data['datos_ria']=$history->getDatosHistoriaPatoLiq($datos_historia['tipo_documento'], $datos_historia['documento'], $datos_historia['numero_folio'])->toArray();
        }






        $model_data['type'] = $_POST['type'];

        $model_data['cod_ing']=$_POST['num_ing'];

         $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }

    public function eliminarCupSiaAction()
    {

        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $id_ria=$_POST['id_ria'];
        $cod_cup=$_POST['cod_cup'];
        $id_registro=$_POST['id_capa'];

        try {

             $history->eliminarCupSia($id_ria,$cod_cup );

             $model_data['id_registro']= $id_registro;
             $model_data['proceso']='exito';
             $model_data['mensaje']='Cup eliminado correctamente';


        } catch (\Exception $e) {



            $view = new ViewModel(array('mensaje' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'proceso' => 'error'));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;

        }


        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;

    }

    public function addRiaadmAction()
    {

        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);


        $procedimientos=$_POST['procedimiento'];

        try {

            if (empty($_POST['nro_autorizacion']))
                throw new \Exception("Número de autorización es obligatorio");

            if (empty($_POST['fecha_procedimiento']))
                throw new \Exception("Fecha de procedimiento es obligatorio");

            if (empty($_POST['persona_atiende']))
                throw new \Exception("Personal que atiende es obligatorio");

            if (empty($_POST['clase_procedimiento']))
                throw new \Exception("Clase de procedimiento es obligatorio");

            if (empty($_POST['condicion_usuaria']))
                throw new \Exception("Condicion usuario es obligatorio");

            if (empty($_POST['cantidad_procedimiento']))
                throw new \Exception("Cantidad procedimiento es obligatorio");

            if (empty($_POST['tipo_procedimiento']))
                throw new \Exception("Tipo de procedimiento es obligatorio");

            if (empty($_POST['codigo_diagnostico']))
                throw new \Exception("Código de diagnostico es obligatorio");

             if (empty($_POST['cod_orden']))
                throw new \Exception("Código de orden es obligatorio");

            if($_POST['cantidad_procedimiento']<=0)
            {
                throw new \Exception("Cantidad debe ser mayor a cero");
            }

            $separadores=substr_count($_POST['fecha_procedimiento'], '/');
            //var_dump($separadores);
            if($separadores<2)
            {
                throw new \Exception("Fecha de procedimiento no tiene formato valido. Por favor use dia/mes/año");
            }

             $fecha_procedimiento = explode('/', $_POST['fecha_procedimiento']);

             if (( !checkdate($fecha_procedimiento[1], $fecha_procedimiento[0], $fecha_procedimiento[2]))  )
             {
                 throw new \Exception("Fecha de procedimiento no es una fecha valida. ");

            }

            $fecha_pro_post=$fecha_procedimiento[2].$fecha_procedimiento[1].$fecha_procedimiento[0];

            $fecha_actual=date("Ymd");

            if( (int) $fecha_pro_post > (int) $fecha_actual )
            {
                throw new \Exception("Fecha de procedimiento no puede ser  superior a la fecha actual");

            }

            if(isset($_POST['fur']))
            {

             $separadores=substr_count($_POST['fur'], '/');

            if($separadores<2)
            {
                throw new \Exception("Fecha de FUR no tiene formato valido. Por favor use dia/mes/año");
            }

             $fecha_fur = explode('/', $_POST['fur']);

            if (( !checkdate($fecha_fur[1], $fecha_fur[0], $fecha_fur[2]))  )
            {
                 throw new \Exception("Fecha de FUR no es una fecha valida. ");

            }

            $fecha_pro_fur=$fecha_fur[2].$fecha_fur[1].$fecha_fur[0];

            $fecha_actual=date("Ymd");

            if( (int) $fecha_pro_fur > (int) $fecha_actual )
            {
                throw new \Exception("Fecha de FUR no puede ser  superior a la fecha actual");

            }

            }



             $ria=$history->getMaximoIdRia()->toArray();

             $ria=$ria[0];

             $id_ria=$ria['maximo'];

             $history->guardarRia($_POST, $id_ria);

             $i=0;
            while($i<count($procedimientos))
            {
                if($procedimientos[$i]!="" and $_POST['valor_total'][$i]!="" and  $_POST['valor_copago'][$i]!="" and  $_POST['valor'][$i]!="" )
                {
                    if(!$history->existeCups($id_ria, $procedimientos[$i] ))
                        $history->guardarCupRia($id_ria, $procedimientos[$i], $_POST['valor_total'][$i], $_POST['valor_copago'][$i], $_POST['valor'][$i]);
                }

                $i++;
            }






             $model_data['proceso']='exito';
             $model_data['mensaje']='Registro guardado correctamente';


        } catch (\Exception $e) {



            $view = new ViewModel(array('mensaje' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'proceso' => 'error'));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;

        }


        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;


    }

    public function actualizarRiaAction()
    {

         $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $procedimientos=$_POST['procedimiento'];

        try {

            if (empty($_POST['nro_autorizacion']))
                throw new \Exception("Número de autorización es obligatorio");

            if (empty($_POST['fecha_procedimiento']))
                throw new \Exception("Fecha de procedimiento es obligatorio");

            if (empty($_POST['persona_atiende']))
                throw new \Exception("Personal que atiende es obligatorio");

            if (empty($_POST['clase_procedimiento']))
                throw new \Exception("Clase de procedimiento es obligatorio");

            if (empty($_POST['condicion_usuaria']))
                throw new \Exception("Condicion usuario es obligatorio");

            if (empty($_POST['cantidad_procedimiento']))
                throw new \Exception("Cantidad procedimiento es obligatorio");

            if (empty($_POST['tipo_procedimiento']))
                throw new \Exception("Tipo de procedimiento es obligatorio");

            if (empty($_POST['codigo_diagnostico']))
                throw new \Exception("Código de diagnostico es obligatorio");

            if (empty($_POST['cod_orden']))
                throw new \Exception("Código de orden es obligatorio");


            if($_POST['cantidad_procedimiento']<=0)
            {
                throw new \Exception("Cantidad debe ser mayor a cero");
            }


            $separadores=substr_count($_POST['fecha_procedimiento'], '/');

            if($separadores<2)
            {
                throw new \Exception("Fecha de procedimiento no tiene formato valido. Por favor use dia/mes/año");
            }

             $fecha_procedimiento = explode('/', $_POST['fecha_procedimiento']);

            if (( !checkdate($fecha_procedimiento[1], $fecha_procedimiento[0], $fecha_procedimiento[2]))  )
            {
                 throw new \Exception("Fecha de procedimiento no es una fecha valida. ");

            }

            $fecha_pro_post=$fecha_procedimiento[2].$fecha_procedimiento[1].$fecha_procedimiento[0];

            $fecha_actual=date("Ymd");

            if( (int) $fecha_pro_post > (int) $fecha_actual )
            {
                throw new \Exception("Fecha de procedimiento no puede ser  superior a la fecha actual");

            }

            if(isset($_POST['fur']))
            {

             $separadores=substr_count($_POST['fur'], '/');

            if($separadores<2)
            {
                throw new \Exception("Fecha de FUR no tiene formato valido. Por favor use dia/mes/año");
            }

             $fecha_fur = explode('/', $_POST['fur']);

            if (( !checkdate($fecha_fur[1], $fecha_fur[0], $fecha_fur[2]))  )
            {
                 throw new \Exception("Fecha de FUR no es una fecha valida. ");

            }

            $fecha_pro_fur=$fecha_fur[2].$fecha_fur[1].$fecha_fur[0];

            $fecha_actual=date("Ymd");

            if( (int) $fecha_pro_fur > (int) $fecha_actual )
            {
                throw new \Exception("Fecha de FUR no puede ser  superior a la fecha actual");

            }

            }





             $id_ria=$_POST['id_ria'];

             $history->actualizarRia($_POST, $id_ria);

             $i=0;
            while($i<count($procedimientos))
            {
                if($procedimientos[$i]!="" and $_POST['valor_total'][$i]!="" and  $_POST['valor_copago'][$i]!="" and  $_POST['valor'][$i]!="" )
                {
                    if(!$history->existeCups($id_ria, $procedimientos[$i] ))
                        $history->guardarCupRia($id_ria, $procedimientos[$i], $_POST['valor_total'][$i], $_POST['valor_copago'][$i], $_POST['valor'][$i]);
                }

                $i++;
            }


             $model_data['proceso']='exito';
             $model_data['mensaje']='Registro actualizado correctamente';


        } catch (\Exception $e) {



            $view = new ViewModel(array('mensaje' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'proceso' => 'error'));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;

        }

        $dbAdapter = $this->getDbAdapter();
        $history = new HistoryTable($dbAdapter);

        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
    }



	public function addAdmissionAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("admissionPatients"))
                throw new \Exception("No tienes permiso para admisionar pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $form = new AdmissionForm($this);
        $form->get('submit')->setValue('Agregar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        if ($request->isPost())
        {
            $admission = new Admission();
            $form->setValidationGroup(
                'cod_tip_doc', 'num_doc_pac', 'cod_usu_med', 'cod_are', 'cod_ent', 'obs_adm'
            );
            $form->setInputFilter($admission->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $admission->exchangeArray($form->getData());
                try {
                	$auth = new AuthenticationService();
                	$admission->cod_usu_reg = $auth->getIdentity()->cod_usu;
                    $response = $this->getAdmissionTable()->addAdmission($admission);
                    $model_data['Success'] = true;
                    $model_data['cod_adm'] = $response["cod_adm"];
                }
                catch (\Exception $e) {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['noExiste'] = $e->getCode();
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

	public function patientAdmissionAction()
	{
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("admissionPatients"))
                throw new \Exception("No tienes permiso para admisionar pacientes");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', 0);

        if (empty($id) || $type == 0)
            return $this->redirect()->toRoute('admissions', array(
                'action' => 'index'
            ));

        $form = new AdmissionForm($this);
        $form->get('submit')->setAttribute('value', 'Admisionar');

        $model_data['xmlHttpRequest'] = ($xmlHttpRequest) ? true : false;

        $request = $this->getRequest();

        if ($request->isGet()) {
	        try {
	            $patient = $this->getPatientTable()->getPatient($id, $type);
	        }
	        catch (\Exception $e) {
	            $model_data['Exception'] = $e->getMessage();
	            $view = new ViewModel($model_data);
	            if ($xmlHttpRequest)
	                $view->setTerminal(true);
	            return $view;
	        }
	        $form->bind($patient);
        }

		$model_data['form'] = $form;

        if ($request->isPost())
        {
            $admission = new Admission();
            $form->setValidationGroup(
                'cod_tip_doc', 'num_doc_pac', 'cod_are', 'cod_ent', 'obs_adm', 'cod_usu_med'
            );
            $form->setInputFilter($admission->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
               	$admission->exchangeArray($form->getData());
                try {
                	$auth = new AuthenticationService();
                	$admission->cod_usu_reg = $auth->getIdentity()->cod_usu;
                    $response = $this->getAdmissionTable()->addAdmission($admission);
                    $model_data['Success'] = true;
                    $model_data['cod_adm'] = $response["cod_adm"];
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

        $model_data['id'] = $id;
        $model_data['type'] = $type;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
	}

	public function viewAdmissionsAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        try {
            if (!$this->isAllow("viewAdmissions"))
                throw new \Exception("No tienes permiso para ver admisiones");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest, 'simulateXmlHttpRequest' => $simulateXmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', 0);

        $options = array();

        $admission = $this->getRequest()->getPost("request");

        # If html element does not exists, params options neither exists and AJAX request wrong (undefined index)
        if (is_array(@$this->getRequest()->getPost("params")) && count(@$this->getRequest()->getPost("params")))
        {
            $params = @$this->getRequest()->getPost("params");
            $time = @$params["time"];
            $limit = @$params["limit"];

            if ($time == '1')
                $options["onlyToday"] = true;
            elseif ($time == '0')
                $options["beforeToday"] = true;
            else
                $options["onlyToday"] = true;

            $options["limit"] = $limit;
        }
        else
            $options["onlyToday"] = true;

        if (!empty($id) && $type != 0) {
			$options['id'] = $id;
			$options['type'] = $type;
        }

        if ($this->getUserTable()->getPermission($this->getIdentity()->cod_usu) == 4)
            $options["cod_usu_med"] = $this->getIdentity()->cod_usu;

        $view = new ViewModel(array(
            'needle' => $admission,
            'admissions' => $this->getAdmissionTable()->search($admission, $options),
            'empty' => ( bool ) !$this->getAdmissionTable()->hasAdmissions($this->getAdmissionTable()->search($admission, $options)),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
            'acl' => $this->configureAcl(),
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);
        return $view;
	}

	public function deleteAdmissionsAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {
            if (!$this->isAllow("deleteAdmissions"))
                throw new \Exception("No tienes permiso para eliminar admisiones");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage(), 'xmlHttpRequest' => $xmlHttpRequest));
            if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if ($xmlHttpRequest) {
            $admissions = $this->getRequest()->getPost("request");
            foreach ($admissions as $admission) {
                $this->getAdmissionTable()->deleteAdmission($admission);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $admissions ) ));
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id == 0)
            return $this->redirect()->toRoute('admissions');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Si') {
                $id = (int) $request->getPost('id');
                $this->getAdmissionTable()->deleteAdmission($id);
            }

            return $this->redirect()->toRoute('admissions');
        }

        return array(
            'id'    => $id,
            'admission' => $this->getAdmissionTable()->getAdmission($id),
        );
	}

	public function annulAdmissionsAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest) {
            $admissions = $this->getRequest()->getPost("request");
            foreach ($admissions as $admission) {
                $this->getAdmissionTable()->annulAdmission($admission);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $admissions ) ));
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        $this->getAdmissionTable()->annulAdmission($id);
        return $this->redirect()->toRoute('admissions');
	}

    public function openAdmissionsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest) {
            $admissions = $this->getRequest()->getPost("request");
            foreach ($admissions as $admission) {
                $this->getAdmissionTable()->openAdmission($admission);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $admissions ) ));
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        $this->getAdmissionTable()->openAdmission($id);
        return $this->redirect()->toRoute('admissions');
    }

    public function closeAdmissionsAction()
	{
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest) {
            $admissions = $this->getRequest()->getPost("request");
            foreach ($admissions as $admission) {
                $this->getAdmissionTable()->closeAdmission($admission);
            }
            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $admissions ) ));
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        $this->getAdmissionTable()->closeAdmission($id);
        return $this->redirect()->toRoute('admissions');
	}

}
