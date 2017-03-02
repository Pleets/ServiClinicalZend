<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use MedicalHistory\Model\Entity\FirstHistory;
use MedicalHistory\Model\Entity\ControlHistory;
use Admissions\Model\Entity\Patient;
use Admissions\Model\Entity\Admission;
use MedicalHistory\Model\Entity\Diagnostic;
use MedicalHistory\Model\Entity\DiagnosticTable;
use MedicalHistory\Model\Entity\Certification;
use MedicalHistory\Model\Entity\CertificationTable;
use MedicalHistory\Model\Entity\Medication;
use MedicalHistory\Model\Entity\Indication;
use MedicalHistory\Model\Entity\Solicitude;
use MedicalHistory\Model\Entity\Interconsultation;
use MedicalHistory\Model\Entity\Incapacity;
use MedicalHistory\Model\Entity\IncapacityTable;
use MedicalHistory\Model\Entity\Ingress;
use MedicalHistory\Model\Entity\PatientDiagnostic;
use MedicalHistory\Model\Entity\PatientMedication;
use MedicalHistory\Model\Entity\PatientExam;
use MedicalHistory\Model\Entity\HistoryTable;
use MedicalHistory\Model\Entity\TypeHistoryTable;
use MedicalHistory\Model\Entity\FirstHistoryTable;
use MedicalHistory\Model\Entity\ControlHistoryTable;
use MedicalHistory\Model\Entity\Observation;
use MedicalHistory\Model\Entity\Patologia;
use MedicalHistory\Model\Entity\Liquidos;
use MedicalHistory\Model\Entity\Adjuntos;
use MedicalHistory\Model\Entity\Citologia;
use MedicalHistory\Model\Entity\Background;
use MedicalHistory\Model\Entity\HistoryTabe;
use MedicalHistory\Model\Entity\AdjuntosTabe;
use MedicalHistory\Model\Entity\BackgroundTable;
use Settings\Model\Entity\MedicationsTable;
use Settings\Model\Entity\SpecialtyTable;
use Settings\Model\Entity\ExamsTable;
use Settings\Model\Entity\IngressTable;
use Settings\Model\Entity\PatientExamTable;
use Settings\Model\Entity\InterconsultationTable;
use MedicalHistory\Form\FirstHistoryForm;
use MedicalHistory\Form\ControlHistoryForm;
use MedicalHistory\Form\ObservationsForm;
use MedicalHistory\Form\PatologiaForm;
use MedicalHistory\Form\LiquidosForm;
use MedicalHistory\Form\CitologiaForm;
use MedicalHistory\Form\DiagnosticForm;
use MedicalHistory\Form\MedicationForm;
use MedicalHistory\Form\IndicationForm;
use MedicalHistory\Form\SolicitudeForm;
use MedicalHistory\Form\InterconsultationForm;
use MedicalHistory\Form\IncapacityForm;
use MedicalHistory\Form\CertificationForm;
use MedicalHistory\Form\BackgroundForm;
use Auth\Model\AclAdapter;

class ManageController extends AbstractActionController
{
    private $patientTable;
    private $admissionTable;
    private $diagnosticTable;
    private $medicationTable;
    private $specialtyTable;
    private $certificationTable;
    private $ingressTable;
    private $examsTable;
    private $adjuntosTable;
    private $patientDiagnosticTable;
    private $patientMedicationTable;
    private $indicationTable;
    private $patientExamTable;
    private $interconsultationTable;
    private $incapacityTable;
    private $typeHistoryTable;
    private $firstHistoryTable;
    private $controlHistoryTable;
    private $observationTable;
    private $patologiaTable;
    private $liquidosTable;
    private $citologiaTable;
    private $dbAdapter;
    private $userTable;
    private $backgroundTable;

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

    private function getAdmissionTable()
    {
        if (!$this->admissionTable) {
            $sm = $this->getServiceLocator();
            $this->admissionTable = $sm->get('Admissions\Model\Entity\AdmissionTable');
        }
        return $this->admissionTable;
    }

    private function getDiagnosticTable()
    {
        if (!$this->diagnosticTable) {
            $sm = $this->getServiceLocator();
            $this->diagnosticTable = $sm->get('MedicalHistory\Model\Entity\DIagnosticTable');
        }
        return $this->diagnosticTable;
    }

    private function getMedicationTable()
    {
        if (!$this->medicationTable) {
            $sm = $this->getServiceLocator();
            $this->medicationTable = $sm->get('Settings\Model\Entity\MedicationsTable');
        }
        return $this->medicationTable;
    }

    private function getExamsTable()
    {
        if (!$this->examsTable) {
            $sm = $this->getServiceLocator();
            $this->examsTable = $sm->get('Settings\Model\Entity\examsTable');
        }
        return $this->examsTable;
    }

    private function getSpecialtyTable()
    {
        if (!$this->specialtyTable) {
            $sm = $this->getServiceLocator();
            $this->specialtyTable = $sm->get('MedicalHistory\Model\Entity\SpecialtyTable');
        }
        return $this->specialtyTable;
    }

    private function getCertificationTable()
    {
        if (!$this->certificationTable) {

            $sm = $this->getServiceLocator();
            $this->certificationTable = $sm->get('MedicalHistory\Model\Entity\CertificationTable');
        }
        return $this->certificationTable;
    }

    private function getIngressTable()
    {
        if (!$this->ingressTable) {
            $sm = $this->getServiceLocator();
            $this->ingressTable = $sm->get('MedicalHistory\Model\Entity\IngressTable');
        }
        return $this->ingressTable;
    }

    private function getPatientDiagnosticTable()
    {
        if (!$this->patientDiagnosticTable) {
            $sm = $this->getServiceLocator();
            $this->patientDiagnosticTable = $sm->get('MedicalHistory\Model\Entity\PatientDiagnosticTable');
        }

        return $this->patientDiagnosticTable;
    }

    private function getAdjuntosTable()
    {
        if (!$this->adjuntosTable) {
            $sm = $this->getServiceLocator();
            $this->adjuntosTable = $sm->get('MedicalHistory\Model\Entity\AdjuntosTable');
        }
        return $this->adjuntosTable;
    }

    private function getPatientMedicationTable()
    {
        if (!$this->patientMedicationTable) {
            $sm = $this->getServiceLocator();
            $this->patientMedicationTable = $sm->get('MedicalHistory\Model\Entity\PatientMedicationTable');
        }
        return $this->patientMedicationTable;
    }

    private function getIndicationTable()
    {
        if (!$this->indicationTable) {
            $sm = $this->getServiceLocator();
            $this->indicationTable = $sm->get('MedicalHistory\Model\Entity\IndicationTable');
        }
        return $this->indicationTable;
    }

    private function getPatientExamTable()
    {
        if (!$this->patientExamTable) {
            $sm = $this->getServiceLocator();
            $this->patientExamTable = $sm->get('MedicalHistory\Model\Entity\PatientExamTable');
        }
        return $this->patientExamTable;
    }

    private function getInterconsultationTable()
    {
        if (!$this->interconsultationTable) {
            $sm = $this->getServiceLocator();
            $this->interconsultationTable = $sm->get('MedicalHistory\Model\Entity\InterconsultationTable');
        }
        return $this->interconsultationTable;
    }

    private function getIncapacityTable()
    {
        if (!$this->incapacityTable) {
            $sm = $this->getServiceLocator();
            $this->incapacityTable = $sm->get('MedicalHistory\Model\Entity\IncapacityTable');
        }
        return $this->incapacityTable;
    }

    private function getFirstHistoryTable()
    {
        if (!$this->firstHistoryTable) {
            $sm = $this->getServiceLocator();
            $this->firstHistoryTable = $sm->get('MedicalHistory\Model\Entity\FirstHistoryTable');
        }
        return $this->firstHistoryTable;
    }

    private function getTypeHistoryTable()
    {
        if (!$this->typeHistoryTable) {
            $sm = $this->getServiceLocator();
            $this->typeHistoryTable = $sm->get('MedicalHistory\Model\Entity\TypeHistoryTable');
        }
        return $this->typeHistoryTable;
    }

    private function getControlHistoryTable()
    {
        if (!$this->controlHistoryTable) {
            $sm = $this->getServiceLocator();
            $this->controlHistoryTable = $sm->get('MedicalHistory\Model\Entity\ControlHistoryTable');
        }
        return $this->controlHistoryTable;
    }

    private function getObservationTable()
    {
        if (!$this->observationTable) {
            $sm = $this->getServiceLocator();
            $this->observationTable = $sm->get('MedicalHistory\Model\Entity\ObservationTable');
        }
        return $this->observationTable;
    }

    private function getPatologiaTable()
    {
        if (!$this->patologiaTable) {
            $sm = $this->getServiceLocator();
            $this->patologiaTable = $sm->get('MedicalHistory\Model\Entity\PatologiaTable');
        }
        return $this->patologiaTable;
    }

    private function getLiquidosTable()
    {
        if (!$this->liquidosTable) {
            $sm = $this->getServiceLocator();
            $this->liquidosTable = $sm->get('MedicalHistory\Model\Entity\LiquidosTable');
        }
        return $this->liquidosTable;
    }

    private function getCitologiaTable()
    {
        if (!$this->citologiaTable) {
            $sm = $this->getServiceLocator();
            $this->citologiaTable = $sm->get('MedicalHistory\Model\Entity\CitologiaTable');
        }
        return $this->citologiaTable;
    }

    private function getBackgroundTable()
    {
        if (!$this->backgroundTable)
        {
            $sm = $this->getServiceLocator();
            $this->backgroundTable = $sm->get('MedicalHistory\Model\Entity\BackgroundTable');

        }

        return $this->backgroundTable;
    }

    public function indexAction()
    {
        $this->authenticate();
        $acl = $this->configureAcl();

        $model_data = array();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        try {

            $firstHistory = $this->getFirstHistoryTable()->search();
            $controlHistory = $this->getControlHistoryTable()->search();
            $patologiaHistory = $this->getPatologiaTable()->search();
            $liquidosHistory = $this->getLiquidosTable()->search();
            $citologiaHistory = $this->getCitologiaTable()->search();

            if (isset($acl) && !$acl->isAllowed("viewHistory"))
                throw new \Exception("No tienes permiso para ver historia clínica", 1);
        }

        catch (\Exception $e) {

            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["firstHistory"] = $firstHistory;
        $model_data["controlHistory"] = $controlHistory;
        $model_data["patologia"] = $patologiaHistory;
        $model_data["liquidos"] = $liquidosHistory;
        $model_data["citologia"] = $citologiaHistory;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewHistoryAction()
    {
        $this->authenticate();
        $acl = $this->configureAcl();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $dbAdapter = $this->getDbAdapter();

            $historyTable = new HistoryTable($dbAdapter);

            $result = $historyTable->getFoliosForPatient($admission->num_doc_pac, $admission->cod_tip_doc);
            $model_data["folios"] = $result;

            $result = $historyTable->getFolioByAdmission($admission->cod_adm);

            $model_data["folio"] = $result;
            $model_data["admission"] = $admission;

            $types = $this->getTypeHistoryTable()->fetchAll();

            $types_h = array();

            foreach ($types as $key => $_type)
            {
                $types_h[$_type["cod_tip_his"]] = $_type["nom_tip_his"];
            }

            $model_data["type"] = $type;

            $model_data["typeHistory"] = $types_h;

            if (!$acl->isAllowed("viewHistory"))
                throw new \Exception("No tienes permiso para ver historia clínica", 1);

        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModl($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }


        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewHcfoliosAction()

    {

        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        $id = (int) $this->params()->fromRoute('id', 0);
		$type = (int) $this->params()->fromRoute('type', 0);

        $admission = $this->getAdmissionTable()->getFullAdmission($id);

        $dbAdapter = $this->getDbAdapter();


        $historyTable = new HistoryTable($dbAdapter);

        $folios = $historyTable->getFoliosForPatient($admission->num_doc_pac, $admission->cod_tip_doc);


        $types = $this->getTypeHistoryTable()->fetchAll();

        $types_h = array();


        foreach ($types as $key => $_type)
        {
            $types_h[$_type["cod_tip_his"]] = $_type["nom_tip_his"];
        }

        $view = new ViewModel(array(
            'type' => $type,
            'folios' => $folios,
            'admission' => $admission,
            'typeHistory' => $types_h,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function viewHcdiagnosticsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");
		$id = (int) $this->params()->fromRoute('id', 0);

        $admission = $this->getAdmissionTable()->getFullAdmission($id);
        $dbAdapter = $this->getDbAdapter();
        $historyTable = new HistoryTable($dbAdapter);
        $diagnostics = $historyTable->getDiagnosticsByAdmission($admission->cod_adm);

        $view = new ViewModel(array(
            'diagnostics' => $diagnostics,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewHcmedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

		$id = (int) $this->params()->fromRoute('id', 0);
        $admission = $this->getIngressTable()->getFullIngress($id);
        $dbAdapter = $this->getDbAdapter();

        $historyTable = new HistoryTable($dbAdapter);

        $medications = $historyTable->getMedicationsByAdmission($admission->cod_adm);

        $view = new ViewModel(array(
            'medications' => $medications,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function viewHcexamsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

		$id = (int) $this->params()->fromRoute('id', 0);
        $admission = $this->getIngressTable()->getFullIngress($id);
        $dbAdapter = $this->getDbAdapter();

        $historyTable = new HistoryTable($dbAdapter);

        $exams = $historyTable->getExamsByAdmission($admission->cod_adm);

        $view = new ViewModel(array(
            'exams' => $exams,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function viewHcinterconsultationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");
        $id = (int) $this->params()->fromRoute('id', 0);
		$type = (int) $this->params()->fromRoute('type', 0);

        $admission = $this->getIngressTable()->getFullIngress($id);

        $dbAdapter = $this->getDbAdapter();

        $interconsultation = null;

        if ($type !== 0)
            $interconsultation = $this->getInterconsultationTable()->getFullInterconsultationByCode($type);

        $historyTable = new HistoryTable($dbAdapter);
        $interconsultations = $historyTable->getInterconsultationsByAdmission($admission->cod_adm);

        $view = new ViewModel(array(
            'interconsultations' => $interconsultations,
            'interconsultation' => $interconsultation,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewHcincapacitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        $id = (int) $this->params()->fromRoute('id', 0);
        $admission = $this->getIngressTable()->getFullIngress($id);
        $dbAdapter = $this->getDbAdapter();

        $historyTable = new HistoryTable($dbAdapter);
        $incapacities = $historyTable->getIncapacitiesByAdmission($admission->cod_adm);

        $parsedIncapacities = $incapacities->toArray();
        $final = null;

        if (count($parsedIncapacities))
        {
            $incap = $parsedIncapacities[0];

            # days of incapacity
            $final = strtotime ( '+'.($incap["num_dia_inc"] - 1).' day' , strtotime ( $incap["fec_ini_inc"] ) );
            $final = date ( 'Y-m-d' , $final );
        }

        $view = new ViewModel(array(
            'incapacities' => $historyTable->getIncapacitiesByAdmission($admission->cod_adm),
            'final' => $final,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function viewHccertificationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        $id = (int) $this->params()->fromRoute('id', 0);

        $type = (int) $this->params()->fromRoute('type', 0);

        $certification = null;

        if ($type !== 0)
            $certification = $this->getCertificationTable()->getCertification($type);

        $admission = $this->getIngressTable()->getFullIngress($id);
        $dbAdapter = $this->getDbAdapter();


        $historyTable = new HistoryTable($dbAdapter);
        $certifications = $historyTable->getCertificationsByAdmission($admission->cod_adm);


        $view = new ViewModel(array(
            'certifications' => $certifications,
            'certification' => $certification,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }


    public function viewHcobservationsAction()
    {
        $this->authenticate();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        $id = (int) $this->params()->fromRoute('id', 0);

        $admission = $this->getIngressTable()->getFullIngress($id);
        $dbAdapter = $this->getDbAdapter();

        $historyTable = new HistoryTable($dbAdapter);
        $observations = $historyTable->getObservationsByAdmission($admission->cod_adm);

        $view = new ViewModel(array(
            'observations' => $observations,
            'admission' => $admission,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,

        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }


    public function viewHchistoryAction()
    {
        $this->authenticate();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $simulateXmlHttpRequest = is_null($this->getRequest()->getPost("simulateXmlHttpRequest")) ?  $xmlHttpRequest: $this->getRequest()->getPost("simulateXmlHttpRequest");

        $id = (int) $this->params()->fromRoute('id', 0);
        $type = (int) $this->params()->fromRoute('type', 0);

        try {

            if ($type == 0)
                throw new \Exception("No se ha seleccionado tipo de historia y folio!");

            $admission = $this->getIngressTable()->getFullIngress($id);

            $dbAdapter = $this->getDbAdapter();

            $historyTable = new HistoryTable($dbAdapter);
            $exams = $historyTable->getExamsByAdmission($admission->cod_adm);
            $diagnostics = $historyTable->getDiagnosticsByAdmission($admission->cod_adm);
            $medications = $historyTable->getMedicationsByAdmission($admission->cod_adm);
            $backgrounds = $historyTable->getBackgroundsByPatient($admission->num_doc_pac, $admission->cod_tip_doc);
            $history = $historyTable->getHistory($admission->num_doc_pac, $admission->cod_tip_doc, $admission->num_fol, $type);

            $form = array();

            if ($history->cod_tip_his == 6)
            {
                $form_data = $this->getCitologiaTable()->getCitologia($admission->num_doc_pac, $admission->cod_tip_doc, $admission->num_fol);
                $form = new CitologiaForm($this);
                $form->setData( (array) $form_data );
            }
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;

            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'form' => $form,
            'exams' => $exams,
            'diagnostics' => $diagnostics,
            'medications' => $medications,
            'backgrounds' => $backgrounds,
            'admission' => $admission,
            'history' => $history,
            'type' => $history->cod_tip_his,
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => ( bool ) $simulateXmlHttpRequest,
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function admissionAction()
	{
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data["acl"] = $acl = $this->configureAcl();
        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $id = $this->getRequest()->getPost('id');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_adm"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $model_data["open"] = (empty($admission->est_adm)) ? true : false;
            $model_data["admission"] = $admission;

            if (!$acl->isAllowed("viewHistory") && !$acl->isAllowed("addHistory"))

                throw new \Exception("No tienes permiso para ver o registrar historias clínicas", 1);

        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;

            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addHistoryAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $acl = $this->configureAcl();

        $action = $this->getRequest()->getPost('action');
        $type = $this->getRequest()->getPost('type');

        // Second post action (form validation)

        $spa = (string) $this->params()->fromRoute('type', "");

        if ($xmlHttpRequest && $action == "get")
            $id = $this->getRequest()->getPost('id');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_adm"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', "");

        $model_data["type"] = (!is_null($type)) ? $type : $spa;

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $model_data["admission"] = $admission;

            if (!in_array($type, array("firstHistory", "controlHistory", "observations", "patologia", "liquidos", "citologia")) && !in_array($spa, array("firstHistory", "controlHistory", "observations", "patologia", "liquidos", "citologia")))
                throw new \Exception("No se ha definido un tipo de historia...!");

            if (!$acl->isAllowed("addHistory"))
                throw new \Exception("No tienes permiso para registrar historia clínica", 111);

            if ($admission->cod_usu_med != $this->getIdentity()->cod_usu)
                throw new \Exception("El paciente fue admisionado a otro médico!", 110);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
            {
                $auth = new AuthenticationService();
                $user = $auth->getIdentity()->cod_usu;

                function getRealIP()
                {
                    if (!empty($_SERVER['HTTP_CLIENT_IP']))
                        return $_SERVER['HTTP_CLIENT_IP'];
                    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                        return $_SERVER['HTTP_X_FORWARDED_FOR'];

                    return $_SERVER['REMOTE_ADDR'];
                }

                $data = array(
                    "hcingresos" => array(
                        "cod_adm" => $admission->cod_adm,
                        "num_doc_pac" => $admission->num_doc_pac,
                        "cod_tip_doc" => $admission->cod_tip_doc,
                        "num_fol" => null,
                        "cod_usu_med" => $admission->cod_usu_med,
                        "cod_tip_his" => $type,
                        "fecha_reg" => null,
                    ),
                    "log" => array(
                        "user" => $user,
                        "date" => date("Y-m-d H:i:s"),
                        "ip" => getRealIP()
                    )
                );

                $response = \Zend\Json\Json::encode( $data );

                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
        }

        catch (\Exception $e) {

            $model_data['Exception'] = $e;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;

        }


        // Assigns folio
        $exists = $this->getIngressTable()->isIngress($admission->cod_adm);

        if (!$exists) {
        	$folio = $this->getIngressTable()->getLastFolio($admission->num_doc_pac, $admission->cod_tip_doc) + 1;
        }
        else {
        	$folio = $this->getIngressTable()->getFolio($admission->cod_adm);
        }

        try {

            switch ($model_data["type"]) {

                case 'firstHistory':

                    $form = new FirstHistoryForm($this);

    				if ($this->getFirstHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
    					$form->setData( (array) $this->getFirstHistoryTable()->getHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio));

                    if ($this->getControlHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                        throw new \Exception("La historia clínica fue registrada como CONTROL!", 100);

                    break;

                case 'controlHistory':

                    $form = new ControlHistoryForm($this);

                    if ($this->getControlHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                        $form->setData( (array) $this->getControlHistoryTable()->getHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio));

                    if ($this->getFirstHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                        throw new \Exception("La historia clínica fue registrada como PRIMERA VEZ!", 101);

                    break;

                case 'observations':

                    $form = new ObservationsForm($this);

                    if ($this->getObservationTable()->isObservation($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
    					$form->setData( (array) $this->getObservationTable()->getObservation($admission->num_doc_pac, $admission->cod_tip_doc, $folio));

                    break;

                case 'patologia':

                    # multimedia
                    $adjuntos = $this->getAdjuntosTable()->getAdjuntos($admission->num_doc_pac, $admission->cod_tip_doc, $folio, 4);
                    $model_data["adjuntos"] = $adjuntos;

                    $form = new PatologiaForm($this);

                    if ($this->getPatologiaTable()->isPatologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
    					$form->setData( (array) $this->getPatologiaTable()->getPatologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio));

                    break;

               case 'liquidos':

                    # multimedia
                    $adjuntos = $this->getAdjuntosTable()->getAdjuntos($admission->num_doc_pac, $admission->cod_tip_doc, $folio, 5);
                    $model_data["adjuntos"] = $adjuntos;

                    $form = new LiquidosForm($this);

                    if ($this->getLiquidosTable()->isLiquidos($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
    					$form->setData( (array) $this->getLiquidosTable()->getLiquidos($admission->num_doc_pac, $admission->cod_tip_doc, $folio));

                break;

                  case 'citologia':

                    $form = new CitologiaForm($this);

                    if ($this->getCitologiaTable()->isCitologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                        $form->setData( (array) $this->getCitologiaTable()->getCitologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio));
                break;
            }
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e;
            $model_data['form'] = $form;
            $model_data['admission'] = $admission;
            $model_data['xmlHttpRequest'] = $xmlHttpRequest;

            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["admission"] = $admission;
        $model_data["form"] = $form;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isPost() && !empty($spa))
        {

            switch ($spa)
            {
                case 'firstHistory':

                    $history = new FirstHistory($this);

                    $cod_tip_his = 1;

		            $form->setValidationGroup(
		                'cod_tip_doc', 'num_doc_pac', 'mot_con', 'rev_sis', 'enf_act', 'tas', 'tad', 'tam',
		                'fc', 'fr', 'tem', 'peso', 'talla', 'neu_men', 'cab_cue', 'tor_car', 'abd_dig',
		                'genito', 'ext_ost','ana_con'
		            );

		            break;

                case 'controlHistory':

                    $history = new ControlHistory($this);

                    $cod_tip_his = 2;

                    $form->setValidationGroup(
                        'cod_tip_doc', 'num_doc_pac', 'tip_evo', 'inf_sub', 'tas', 'tad', 'tam',
                        'fc', 'fr', 'tem', 'hal_exa', 'int_par', 'ana_con'
                    );

                    break;

                case 'observations':

                    $history = new Observation($this);

                    $cod_tip_his = 3;

		            $form->setValidationGroup(
		                'cod_tip_doc', 'num_doc_pac', 'obs_med'
		            );

                    break;

                case 'patologia':

                    $history = new Patologia($this);

                    $cod_tip_his = 4;

		            $form->setValidationGroup(
		                'cod_tip_doc', 'num_doc_pac', 'material_estudio', 'diagnostico_clinico', 'descripcion_macroscopica', 'descripcion_microscopica', 'diagnostico', 'nota'
		            );

                    break;

                case 'liquidos':

                    $history = new Liquidos($this);

                    $cod_tip_his = 5;

		            $form->setValidationGroup(
		                'cod_tip_doc', 'num_doc_pac', 'material_estudio', 'diagnostico_clinico', 'descripcion_macroscopica', 'descripcion_microscopica', 'diagnostico', 'nota'
		            );

                    break;

                 case 'citologia':

                    $history = new Citologia($this);

                    $cod_tip_his = 6;

                    $form->setValidationGroup(

                        'cod_tip_doc', 'num_doc_pac' ,
                        'cal_mues_a',
                        'cal_mues_b',
                        'cal_mues_c',
                        'cal_mues_d',
                        'cat_gen_a',
                        'cat_gen_b',
                        'mic_a',
                        'mic_b',
                        'mic_c',
                        'mic_d',
                        'mic_e',
                        'mic_f',
                        'mic_g',
                        'otr_haz_a',
                        'otr_haz_b',
                        'otr_haz_c',
                        'otr_haz_d',
                        'otr_haz_e',
                        'otr_haz_f',
                        'ano_cel_esc_a',
                        'ano_cel_esc_b',
                        'ano_cel_esc_c',
                        'ano_cel_esc_d',
                        'ano_cel_esc_e',
                        'ano_cel_esc_f',
                        'ano_cel_gla_a',
                        'ano_cel_gla_b',
                        'ano_cel_gla_c',
                        'ano_cel_gla_d',
                        'ano_cel_gla_e',
                        'ano_cel_gla_f',
                        'ano_cel_gla_g',
                        'ano_cel_gla_h',
                        'observaciones'
                    );

                    break;
            }

            $form->setInputFilter($history->getInputFilter());
		    $form->setData($request->getPost());

            if ($form->isValid())
            {
                $history->exchangeArray($form->getData());

                try {
                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );
                    $date = date("Y-m-d H:i:s");
                    $auth = new AuthenticationService();
                    $user = $auth->getIdentity()->cod_usu;

                    $history_data = (Object) $form->getData();
                    $history_data->num_doc_pac = $admission->num_doc_pac;
                    $history_data->cod_tip_doc = $admission->cod_tip_doc;
                    $history_data->num_fol = $folio;

                    $history->exchangeArray( (array) $history_data);


                    $hcingresos_data = $data["hcingresos"];
                    $hcingresos_data->cod_tip_his = $cod_tip_his;
                    $hcingresos_data->fecha_reg = $date;
                    $hcingresos_data->cod_usu = $user;
                    $hcingresos_data->num_fol = $folio;

                    $hcingresos = new Ingress();
                    $hcingresos->exchangeJson($hcingresos_data);
                    $existsType = $this->getIngressTable()->isIngressByHistoryType($admission->cod_adm, $cod_tip_his);

                    if (!$existsType)
                    	$this->getIngressTable()->addIngress($hcingresos);

                    // Reverse Json data
                    $data["hcingresos"] = $hcingresos_data;
                    $response = \Zend\Json\Json::encode($data);

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    switch ($spa)
                    {
                    	case 'firstHistory':

                    		if (!$this->getFirstHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio)) {
								if (!$this->getControlHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
									if ($model_data["open"])
                                        $this->getFirstHistoryTable()->addHistory($history);
								else
									throw new \Exception("La historia clínica fue registrada como CONTROL!", 100);
                            }
							else
                                $this->getFirstHistoryTable()->updateHistory($history);
                    		break;

                    	case 'controlHistory':

                    		if (!$this->getControlHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio)) {
								if (!$this->getFirstHistoryTable()->isHistory($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
									if ($model_data["open"])
                                        $this->getControlHistoryTable()->addHistory($history);
								else
									throw new \Exception("La historia clínica fue registrada como PRIMERA VEZ!", 101);
                            }
							else
								$this->getControlHistoryTable()->updateHistory($history);
                    		break;

                    	case 'observations':

                            if (!$this->getObservationTable()->isObservation($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                                $this->getObservationTable()->addObservation($history);
                            else
                                $this->getObservationTable()->updateObservation($history);
                    		break;

                      	case 'patologia':

                            $token = $_POST["token"];

                            if (empty($token))
                                throw new \Exception("Invalid Token!", 1);

                            $shell = new \Drone_FileSystem_Shell();
                            $_files = $shell->ls("cache/temp_dir/" . $token ."/". $type);

                            $audios = array();
                            $images = array();

                            foreach ($_files as $file)
                            {
                                if (in_array($file, array('.', '..')))
                                    continue;

                                # at this point is only a dir
                                $files = $shell->ls("cache/temp_dir/" . $token ."/". $file);

                                foreach ($files as $_file)
                                {

                                    if (in_array($_file, array('.', '..')))
                                        continue;

                                    $name = time() . uniqid() . strstr($_file, '.');

                                    switch ($file)
                                    {
                                        # audio
                                        case '1':
                                            copy("cache/temp_dir/" . $token ."/". $file . "/" . $_file, "cache/multimedia/" . $name);
                                            $audios[] = "cache/multimedia/" . $name;
                                            break;

                                        # image
                                        case '2':
                                            copy("cache/temp_dir/" . $token ."/". $file . "/" . $_file, "cache/multimedia/" . $name);
                                            $images[] = "cache/multimedia/" . $name;
                                            break;
                                    }
                                }
                            }

                            foreach ($audios as $audio)
                            {
                                $adjunto = new Adjuntos($this);

                                $adjunto->exchangeArray(array(
                                    "cod_tip_doc"  => $admission->cod_tip_doc,
                                    "num_doc_pac"  => $admission->num_doc_pac,
                                    "num_fol"      => $folio,
                                    "cod_adm"      => $admission->cod_adm,
                                    "cod_tip_his"  => 4,
                                    "tipo_archivo" => 'AUDIO',
                                    "url_archivo"  => $audio
                                ));

                                $this->getAdjuntosTable()->addAdjuntos($adjunto);
                            }

                            foreach ($images as $image)
                            {
                                $adjunto = new Adjuntos($this);

                                $adjunto->exchangeArray(array(
                                    "cod_tip_doc"  => $admission->cod_tip_doc,
                                    "num_doc_pac"  => $admission->num_doc_pac,
                                    "num_fol"      => $folio,
                                    "cod_adm"      => $admission->cod_adm,
                                    "cod_tip_his"  => 4,
                                    "tipo_archivo" => 'IMAGEN',
                                    "url_archivo"  => $image
                                ));

                                $this->getAdjuntosTable()->addAdjuntos($adjunto);
                            }

                            if (!$this->getPatologiaTable()->isPatologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                                $this->getPatologiaTable()->addPatologia($history);
                            else
                                $this->getPatologiaTable()->updatePatologia($history);

                            # multimedia
                            $adjuntos = $this->getAdjuntosTable()->getAdjuntos($admission->num_doc_pac, $admission->cod_tip_doc, $folio, 4);
                            $model_data["adjuntos"] = $adjuntos;

                    	break;

                      	case 'liquidos':

                            $token = $_POST["token"];

                            if (empty($token))
                                throw new \Exception("Invalid Token!", 1);

                            $shell = new \Drone_FileSystem_Shell();
                            $_files = $shell->ls("cache/temp_dir/" . $token ."/". $type);

                            $audios = array();
                            $images = array();

                            foreach ($_files as $file)
                            {
                                if (in_array($file, array('.', '..')))
                                    continue;

                                # at this point is only a dir
                                $files = $shell->ls("cache/temp_dir/" . $token ."/". $file);

                                foreach ($files as $_file)
                                {

                                    if (in_array($_file, array('.', '..')))
                                        continue;

                                    $name = time() . uniqid() . strstr($_file, '.');

                                    switch ($file)
                                    {
                                        # audio
                                        case '1':
                                            copy("cache/temp_dir/" . $token ."/". $file . "/" . $_file, "cache/multimedia/" . $name);
                                            $audios[] = "cache/multimedia/" . $name;
                                            break;

                                        # image
                                        case '2':
                                            copy("cache/temp_dir/" . $token ."/". $file . "/" . $_file, "cache/multimedia/" . $name);
                                            $images[] = "cache/multimedia/" . $name;
                                            break;
                                    }
                                }
                            }

                            foreach ($audios as $audio)
                            {
                                $adjunto = new Adjuntos($this);

                                $adjunto->exchangeArray(array(
                                    "cod_tip_doc"  => $admission->cod_tip_doc,
                                    "num_doc_pac"  => $admission->num_doc_pac,
                                    "num_fol"      => $folio,
                                    "cod_adm"      => $admission->cod_adm,
                                    "cod_tip_his"  => 5,
                                    "tipo_archivo" => 'AUDIO',
                                    "url_archivo"  => $audio
                                ));

                                $this->getAdjuntosTable()->addAdjuntos($adjunto);
                            }

                            foreach ($images as $image)
                            {
                                $adjunto = new Adjuntos($this);

                                $adjunto->exchangeArray(array(
                                    "cod_tip_doc"  => $admission->cod_tip_doc,
                                    "num_doc_pac"  => $admission->num_doc_pac,
                                    "num_fol"      => $folio,
                                    "cod_adm"      => $admission->cod_adm,
                                    "cod_tip_his"  => 5,
                                    "tipo_archivo" => 'IMAGEN',
                                    "url_archivo"  => $image
                                ));

                                $this->getAdjuntosTable()->addAdjuntos($adjunto);
                            }

                            if (!$this->getLiquidosTable()->isLiquidos($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                                $this->getLiquidosTable()->addLiquidos($history);
                            else
                                $this->getLiquidosTable()->updateLiquidos($history);

                            # multimedia
                            $adjuntos = $this->getAdjuntosTable()->getAdjuntos($admission->num_doc_pac, $admission->cod_tip_doc, $folio, 5);
                            $model_data["adjuntos"] = $adjuntos;

                    	break;

                        case 'citologia':

                            if (!$this->getCitologiaTable()->isCitologia($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
                                $this->getCitologiaTable()->addCitologia($history);
                            else
                                $this->getCitologiaTable()->updateCitologia($history);
                        break;
                    }

                    // Tipos de historia que piden diagnóstico
                    if (in_array($spa, array("firstHistory", "controlHistory", "observations")))
                    {
                        if (array_key_exists('hcdiapac', $data) && $model_data["open"])
                        {
                        	$diagnostics = array();
                        	$hcdiapac_data = (array) $data["hcdiapac"];

                        	$diagnostic_keys = array();

                        	// bind Json buffer
                        	if (count($hcdiapac_data))
                        	{
                                $principal = false;

    	                    	foreach ($hcdiapac_data as $diagnostic)
    	                    	{

    	                    		$diagnostic->cod_tip_doc = $admission->cod_tip_doc;
    	                    		$diagnostic->num_doc_pac = $admission->num_doc_pac;
    	                    		$diagnostic->num_fol = $folio;

                                    if ($diagnostic->dia_pri)
                                        $principal = true;

    	                    		$hcdiapac = new PatientDiagnostic();
    	                    		$hcdiapac->exchangeJson($diagnostic);
    	                    		$diagnostics[] = $hcdiapac;
    	                    	}

                                if ($principal == false)
                                {
                                    $model_data['myException'] = "Debe existir al menos un diagnóstico <strong>principal</strong>";
                                    $model_data['form'] = $form;
                                    $model_data['admission'] = $admission;
                                    $model_data['xmlHttpRequest'] = $xmlHttpRequest;
                                    $view = new ViewModel($model_data);

                                    if ($xmlHttpRequest)
                                        $view->setTerminal(true);

                                    return $view;

                                }

                        	   // Add and update diagnostics

    	                    	foreach ($diagnostics as $diag)
                                {
    	    	                	if (!$this->getPatientDiagnosticTable()->isDiagnostic($admission->num_doc_pac, $admission->cod_tip_doc, $diag->num_fol, $diag->cod_dia))
    		                    		$this->getPatientDiagnosticTable()->addDiagnostic($diag);
    		                    	else
    		                    		$this->getPatientDiagnosticTable()->updateDiagnostic($diag);

                                    $diagnostic_keys[] = $diag->cod_dia;
    	                    	}
    	                    }
                            else
                            {
                                $model_data['myException'] = "Debe existir al menos un diagnóstico...";
                                $model_data['form'] = $form;
                                $model_data['admission'] = $admission;
                                $model_data['xmlHttpRequest'] = $xmlHttpRequest;

                                $view = new ViewModel($model_data);

                                if ($xmlHttpRequest)
                                    $view->setTerminal(true);

                                return $view;

                            }

                        	// Delete diagnostics
                        	$dnostics = $this->getPatientDiagnosticTable()->getPatientDiagnosticsByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                        	foreach ($dnostics as $dnostic)
                            {
                        		if (!in_array($dnostic->cod_dia, $diagnostic_keys))
                        			$this->getPaientDiagnosticTable()->deletePatientDiagnosticByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $dnostic->num_fol, $dnostic->cod_dia);
                        	}
                        }
                        else
                        {
                            $model_data['myException'] = "Debe existir al menos un diagnóstico..";
                            $model_data['form'] = $form;
                            $model_data['admission'] = $admission;
                            $model_data['xmlHttpRequest'] = $xmlHttpRequest;
                            $view = new ViewModel($model_data);

                            if ($xmlHttpRequest)
                                $view->setTerminal(true);

                            return $view;
                        }
                    }

                    if (array_key_exists('hcmedfor', $data) && $model_data["open"] )
                    {
                    	$medications = array();
                    	$hcmedfor_data = (array) $data["hcmedfor"];

                    	$medication_keys = array();

                    	if (count($hcmedfor_data))
                    	{
	                    	// bind Json buffer
	                    	foreach ($hcmedfor_data as $medication)
	                    	{
	                    		$medication->cod_tip_doc = $admission->cod_tip_doc;
	                    		$medication->num_doc_pac = $admission->num_doc_pac;
	                    		$medication->num_fol = $folio;

	                    		$hcmedfor = new PatientMedication();
	                    		$hcmedfor->exchangeJson($medication);
	                    		$medications[] = $hcmedfor;
	                    	}

	                    	// Add and update medications
	                    	foreach ($medications as $med)
                             {
	    	                	if (!$this->getPatientMedicationTable()->isMedication($admission->num_doc_pac, $admission->cod_tip_doc, $med->num_fol, $med->cod_med))
		                    		$this->getPatientMedicationTable()->addMedication($med);
		                    	else
		                    		$this->getPatientMedicationTable()->updateMedication($med);

                                $medication_keys[] = $med->cod_med;
	                    	}
	                    }

                    	// Delete medications
                    	$mcations = $this->getPatientMedicationTable()->getPatientMedicationsByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                    	foreach ($mcations as $mcation)
                        {
                    		if (!in_array($mcation->cod_med, $medication_keys))
                    			$this->getPatientMedicationTable()->deletePatientMedicationByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $mcation->num_fol, $mcation->cod_med);
                    	}
                    }

                    if (array_key_exists('indicaciones', $data) && $model_data["open"] )
                    {
                    	$indications = array();
                    	$indicaciones_data = (array) $data["indicaciones"];

                    	$indication_keys = array();

                    	if (count($indicaciones_data))
                    	{
	                    	// bind Json buffer
	                    	foreach ($indicaciones_data as $indication)
	                    	{
	                    		$indication->cod_tip_doc = $admission->cod_tip_doc;
	                    		$indication->num_doc_pac = $admission->num_doc_pac;
	                    		$indication->num_fol = $folio;

	                    		$indicacion = new Indication();
	                    		$indicacion->exchangeJson($indication);
	                    		$indications[] = $indicacion;
	                    	}

	                    	// Add and update indications
	                    	foreach ($indications as $ind)
                            {
	    	                	if (!$this->getIndicationTable()->isIndication($admission->num_doc_pac, $admission->cod_tip_doc, $ind->num_fol, $ind->cod_ind))
		                    		$this->getIndicationTable()->addIndication($ind);
		                    	else
		                    		$this->getIndicationTable()->updateIndication($ind);
		                    	$indication_keys[] = $ind->cod_ind;
	                    	}
	                    }

                    	// Delete indications
                    	$ications = $this->getIndicationTable()->getPatientIndicationsByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                    	foreach ($ications as $ication)
                        {
                    		if (!in_array($ication->cod_ind, $indication_keys))
                    			$this->getIndicationTable()->deletePatientIndicationByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $ication->num_fol, $ication->cod_ind);
                    	}
                    }

                    if (array_key_exists('hcexapac', $data) && $model_data["open"] )
                    {
                    	$exams = array();
                    	$hcexapac_data = (array) $data["hcexapac"];

                    	$exams_keys = array();

                    	if (count($hcexapac_data))
                    	{
	                    	// bind Json buffer
	                    	foreach ($hcexapac_data as $exam)
	                    	{
	                    		$exam->cod_tip_doc = $admission->cod_tip_doc;
	                    		$exam->num_doc_pac = $admission->num_doc_pac;
	                    		$exam->num_fol = $folio;

	                    		$solicitude = new PatientExam();
	                    		$solicitude->exchangeJson($exam);
	                    		$exams[] = $solicitude;
	                    	}

	                    	// Add and update exams
	                    	foreach ($exams as $exam)
                            {
	    	                	if (!$this->getPatientExamTable()->isExam($admission->num_doc_pac, $admission->cod_tip_doc, $exam->num_fol, $exam->cod_exa))
		                    		$this->getPatientExamTable()->addExam($exam);
		                    	else
		                    		$this->getPatientExamTable()->updateExam($exam);

                                $exams_keys[] = $exam->cod_exa;
	                    	}
	                    }

                    	// Delete exams
                    	$exms = $this->getPatientExamTable()->getPatientExamsByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                        foreach ($exms as $exm)
                        {
                    		if (!in_array($exm->cod_exa, $exams_keys))
                    			$this->getPatientExamTable()->deletePatientExamByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $exm->num_fol, $exm->cod_exa);
                    	}
                    }

                    if (array_key_exists('interconsultas', $data) && $model_data["open"])
                    {
                    	$interconsultations = array();
                    	$interconsultas_data = (array) $data["interconsultas"];
                    	$interconsultation_keys = array();

                    	if (count($interconsultas_data))
                    	{
	                    	// bind Json buffer
	                    	foreach ($interconsultas_data as $item)
	                    	{
	                    		$item->cod_tip_doc = $admission->cod_tip_doc;
	                    		$item->num_doc_pac = $admission->num_doc_pac;
	                    		$item->num_fol = $folio;

	                    		$interconsultation = new Interconsultation();
	                    		$interconsultation->exchangeJson($item);
	                    		$interconsultations[] = $interconsultation;
	                    	}

	                    	// Add and update interconsultations
	                    	foreach ($interconsultations as $int)
                            {
	    	                	if (!$this->getInterconsultationTable()->isPatientInterconsultation($int->cod_int, $int->num_doc_pac, $int->cod_tip_doc, $int->num_fol, $int->cod_dia))
		                    		$this->getInterconsultationTable()->addInterconsultation($int);
		                    	else
		                    		$this->getInterconsultationTable()->updateInterconsultation($int);

                                $interconsultation_keys[] = $int->cod_int;
	                    	}
	                    }

                    	// Delete interconsultations
                    	$inters = $this->getInterconsultationTable()->getInterconsultationsByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                    	foreach ($inters as $inter) {
                    		if (!in_array($inter->cod_int, $interconsultation_keys))
                    			$this->getInterconsultationTable()->deleteInterconsultation($inter->cod_int, $inter->num_doc_pac, $inter->cod_tip_doc, $inter->num_fol, $inter->cod_dia);
                    	}
                    }

                    if (array_key_exists('incapacidad', $data) && $model_data["open"])
                    {
                    	$incapacities = array();
                    	$incapacidad_data = (array) $data["incapacidad"];

                    	if (count($incapacidad_data))
                    	{
	                    	// bind Json buffer
	                    	foreach ($incapacidad_data as $item)
	                    	{
	                    		$item->cod_tip_doc = $admission->cod_tip_doc;
	                    		$item->num_doc_pac = $admission->num_doc_pac;
	                    		$item->num_fol = $folio;

	                    		$incapacity = new Incapacity();
	                    		$incapacity->exchangeJson($item);
	                    		$incapacities[] = $incapacity;
	                    	}

	                    	// Add and update incapacities
	                    	foreach ($incapacities as $inc)
                            {
	    	                	if (!$this->getIncapacityTable()->isIncapacity($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
		                    		$this->getIncapacityTable()->addIncapacity($inc);
		                    	else
		                    		$this->getIncapacityTable()->updateIncapacity($inc);
	                    	}
	                    }
	                    else
	                    	if ($this->getIncapacityTable()->isIncapacity($admission->num_doc_pac, $admission->cod_tip_doc, $folio))
	                    		$this->getIncapacityTable()->deleteIncapacityByFolio($admission->num_doc_pac, $admission->cod_tip_doc, $folio);

                    }

                    $model_data['Success'] = true;

                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e;
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
        else
            $form->bind($admission);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function loadfileAction()
    {
        # Array de datos para enviar a la vista
        $data = array();

        $id   = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', "");

        # TRY-CATCH-BLOCK
        try {

            if (!count($_FILES))
            {
                if (array_key_exists("php_errormsg", $GLOBALS))
                    throw new Exception($GLOBALS["php_errormsg"], 1);

                throw new \Exception("Ha ocurrido un error al leer el archivo!", 1);
            }

            if (empty($id) || empty($type))
                throw new \Exception("Invalid Token!", 1);

            $token = $id;

            $files_uploaded = array();

            $shell = new \Drone_FileSystem_Shell();
            $_files = $shell->ls("cache/temp_dir/" . $token ."/". $type);
            $shell->rm("cache/temp_dir/" . $token ."/". $type, true);

            foreach ($_FILES as $file)
            {
                # code...
                $bytes_uploaded_file = $file["size"];

                if ($bytes_uploaded_file > 2097152)
                    throw new \Exception("Error al subir archivo superior a 2MB", 300);

                $data["type"] = $type;

                switch ($type)
                {
                    case '1':
                        if (!in_array($file["type"], explode(",", "audio/mp4,audio/x-m4a")))
                            throw new \Exception("Tipo de archivo no admitido!. Utilice mp3, mp4.", 1);
                        break;

                    case '2':
                        if (!in_array($file["type"], explode(",", "image/jpeg,image/pjpeg,image/png,application/pdf")))
                            throw new \Exception("Tipo de archivo no admitido!. Utilice png o jpg.", 1);
                        break;
                }

                $bytes_uploaded_file = $file["size"];

                if (!file_exists("cache/temp_dir/" . $token))
                    mkdir("cache/temp_dir/" . $token);

                if (!file_exists("cache/temp_dir/" . $token ."/". $type))
                    mkdir("cache/temp_dir/" . $token ."/". $type);

                if (!copy($file["tmp_name"], "cache/temp_dir/" . $token ."/". $type ."/". $file["name"]))
                    throw new Exception("Error al procesar archivo '" . $token ."/". $type ."/". $file["name"] . "'");

                $files_uploaded[] = $token ."/". $type ."/". $file["name"];
            }

            $data["name"] = $file["name"];
            $data["files"] = $files_uploaded;

            # SUCCESS-MESSAGE
            $data["process"] = "success";
        }
        catch (\Exception $e) {

            $data['Exception'] = $e;

            $view = new ViewModel($data);
            $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel($data);
        $view->setTerminal(true);

        return $view;
    }

    public function addDiagnosticAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["admission"] = $admission;
        $form = new DiagnosticForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;
        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        // Refactorizar ... (código repetido)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $diagnostic = new Diagnostic();

                if (isset($data->hcdiapac))
                    $model_data['diagnostics'] = $data->hcdiapac;
                else
                    $model_data['diagnostics'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;

                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $diagnostic = new Diagnostic();

            $form->setValidationGroup(
                'cod_dia', 'nom_dia','dia_pri', 'tip_dia', 'clasi_dia',
                'clase_dia', 'dia_ing', 'dia_egr', 'obs_dia'
            );

            $form->setInputFilter($diagnostic->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $diagnostic->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;

                    $model_data["form"] = new DiagnosticForm($this);
                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

                    if (!isset($data['hcdiapac']))
                        $data['hcdiapac'] = array();
                    else
                        $data['hcdiapac'] = (array) $data['hcdiapac'];


                    foreach ($data['hcdiapac'] as $diag)
                    {
                        $gdata = $form->getData();

                        if ($diag->cod_dia == $gdata["cod_dia"])
                                throw new \Exception("No se puede ingresar dos veces el mismo diagnóstico", 1);
                    }

                    foreach ($data['hcdiapac'] as $diag)
                    {

                        $gdata = $form->getData();

                        if ($diag->dia_pri == "1")
                        {
                            if ($gdata["dia_pri"])
                                throw new \Exception("No pueden existir dos diagnósticos principales", 1);
                        }
                    }

                    $data['hcdiapac'][$diagnostic->cod_dia] = $form->getData();

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);
                }
                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $diagnostic = new Diagnostic();

                if (isset($data->hcdiapac))
                    $model_data['diagnostics'] = $data->hcdiapac;
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function searchDiagnosticAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $term = (string) $_GET["term"];         // BUG [Debería utilizarse from->route pero no funciona]

        try {

            $diagnostic = $this->getDiagnosticTable()->search($term, array("limit" => 30));
            $diagnostics = $diagnostic->toArray();

            $data = array();

            foreach ($diagnostics as $diagnostic) {
                $data[] = array(
                    "id" => $diagnostic["cod_dia"],
                    "value" => $diagnostic["cod_dia"]." - ".$diagnostic["nom_dia"],
                );
            }
        }

        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();

            $data = array(
                0 => array(
                    "id" => "error",
                    "value" => $e->getMessage()
                )
            );

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

            return $response;
        }

        $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

        return $response;
    }

    public function viewDiagnosticsAction()
    {
        $this->authenticate();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

	        $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

	        if (!isset($data['hcdiapac']))
	            $data['hcdiapac'] = array();
	        else
	            $data['hcdiapac'] = (array) $data['hcdiapac'];
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'diagnostics' => $data['hcdiapac'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteDiagnosticsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
			$id = (string) $this->params()->fromRoute('id', "");

	        try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
	            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
	                throw new \Exception("No se ha podido cargar el buffer");
	        }
	        catch (\Exception $e)
            {
	            $model_data['Exception'] = $e->getMessage();
	            $model_data['admission'] = null;
	            $view = new ViewModel($model_data);

	            if ($xmlHttpRequest)
	                $view->setTerminal(true);

	            return $view;
	        }

	        $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

	        if (!isset($data['hcdiapac']))
	            $data['hcdiapac'] = array();
	        else
	            $data['hcdiapac'] = (array) $data['hcdiapac'];

            $diagnostics = $this->getRequest()->getPost("request");

            // [DEPRECATED] - BUG Array with string keys such as '1', '2', ...

            /*foreach ($diagnostics as $diagnostic) {

                unset($data['hcdiapac'][$diagnostic]);

            }*/

            $hcdiapac = array();

            foreach ($data['hcdiapac'] as $key => $value)
            {
                if (!in_array($key, $diagnostics))
                    $hcdiapac[$key] = $value;
            }

            $data['hcdiapac'] = $hcdiapac;

            $response = \Zend\Json\Json::encode( $data );

            try {
                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e)
            {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $diagnostics ) ));
            return $response;
        }
    }

    public function editDiagnosticAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $diagnostic_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $diagnostic_code = $this->getRequest()->getPost('request');
            $diagnostic_code = $diagnostic_code["cod_dia"];
        }
        else
            $diagnostic_code = (string) $this->params()->fromRoute('type', "");

        $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;

        }

        $form  = new DiagnosticForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

            $diagnostic = new Diagnostic();

            if (isset($data->hcdiapac))
                $model_data['diagnostics'] = $data->hcdiapac;
            else
                throw new \Exception("No existen diagnósticos registrados");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $hcdiapac = array();

        foreach ((array)$data->hcdiapac as $key => $value)
        {
                $hcdiapac[$key] = $value;
        }

        $bind = $data->hcdiapac = $hcdiapac;

        // [DEPRECATED] - BUG Array with string keys such as '1', '2', ...
        //$bind = $data->hcdiapac = (array)$data->hcdiapac;
        $diagnostic->exchangeJson($bind[$diagnostic_code]);
        $form->bind($diagnostic);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_dia', 'nom_dia','dia_pri', 'tip_dia', 'clasi_dia',
                'clase_dia', 'dia_ing', 'dia_egr', 'obs_dia'
            );

            $form->setInputFilter($diagnostic->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));


            if ($form->isValid())
            {
                try {

                    $bind[$diagnostic_code] = $request->getPost("request");

                    foreach ($data->hcdiapac as $diag)
                    {
                        $diag = (array) $diag;
                        if ($diag['dia_pri'] == "1")
                        {

                            if ($bind[$diagnostic_code]["dia_pri"] == "1" && $bind[$diagnostic_code]["cod_dia"] != $diag['cod_dia'])
                                throw new \Exception("No pueden existir dos diagnósticos principales", 1);
                        }
                    }

                    $data->hcdiapac[$diagnostic_code] = $bind[$diagnostic_code];
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;

                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);

                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;

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

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;

        }
        $model_data["admission"] = $admission;

        $form = new MedicationForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;
        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        // Refactorizar ... (código repetido)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $medication = new Medication();

                if (isset($data->hcmedfor))
                    $model_data['medications'] = $data->hcmedfor;
                else
                    $model_data['medications'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $medication = new PatientMedication();

            $form->setValidationGroup(
                'cod_med', 'nom_med', 'can_med', 'ter_med', 'cod_apl_med', 'num_dia', 'pos_med'
            );

            $form->setInputFilter($medication->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $medication->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;

                    $model_data["form"] = new MedicationForm($this);

                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

                    if (!isset($data['hcmedfor']))
                        $data['hcmedfor'] = array();
                    else
                        $data['hcmedfor'] = (array) $data['hcmedfor'];

                    foreach ($data['hcmedfor'] as $med)
                    {
                        $gdata = $form->getData();

                        if ($med->cod_med == $gdata["cod_med"])
                                throw new \Exception("No se puede ingresar dos veces el mismo medicamento", 1);
                    }

                    $data['hcmedfor'][$medication->cod_med] = $form->getData();

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                }
                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $medication = new Medication();

                if (isset($data->hcmedfor))
                    $model_data['medications'] = $data->hcmedfor;
                else
                    $model_data['medications'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);
        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function searchMedicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $term = (string) $_GET["term"];         // BUG [Debería utilizarse from->route pero no funciona]

        try {
            $medication = $this->getMedicationTable()->search($term, array("limit" => 30));
            $medications = $medication->toArray();

            $data = array();

            foreach ($medications as $medication)
            {
                $data[] = array(
                    "id" => $medication["cod_med"],
                    "value" => $medication["cod_med"]." - ".$medication["nom_med"],
                );
            }
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();

            $data = array(
                0 => array(
                    "id" => "error",
                    "value" => $e->getMessage()
                )
            );

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

            return $response;
        }

        $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

        return $response;
    }

    public function viewMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['hcmedfor']))
                $data['hcmedfor'] = array();
            else
                $data['hcmedfor'] = (array) $data['hcmedfor'];

        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'medications' => $data['hcmedfor'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteMedicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $id = (string) $this->params()->fromRoute('id', "");

            try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
                $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
                    throw new \Exception("No se ha podido cargar el buffer");
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['admission'] = null;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['hcmedfor']))
                $data['hcmedfor'] = array();
            else
                $data['hcmedfor'] = (array) $data['hcmedfor'];

            $medications = $this->getRequest()->getPost("request");

            $hcmedfor = array();

            foreach ($data['hcmedfor'] as $key => $value)
            {
                if (!in_array($key, $medications))
                    $hcmedfor[$key] = $value;
            }

            $data['hcmedfor'] = $hcmedfor;
            $response = \Zend\Json\Json::encode( $data );

            try {
                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e) {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $medications ) ));

            return $response;
        }
    }

    public function editMedicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")

            $medication_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $medication_code = $this->getRequest()->getPost('request');
            $medication_code = $medication_code["cod_med"];
        }
        else
            $medication_code = (string) $this->params()->fromRoute('type', "");

        $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new MedicationForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
            $medication = new Medication();

            if (isset($data->hcmedfor))
                $model_data['medications'] = $data->hcmedfor;
            else
                throw new \Exception("No existen medicamentos registrados");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $hcmedfor = array();

        foreach ((array)$data->hcmedfor as $key => $value)
        {
                $hcmedfor[$key] = $value;
        }

        $bind = $data->hcmedfor = $hcmedfor;
        $medication->exchangeJson($bind[$medication_code]);
        $form->bind($medication);

        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_med', 'nom_med', 'can_med', 'ter_med', 'cod_apl_med', 'num_dia', 'pos_med'
            );

            $form->setInputFilter($medication->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {

                    $bind[$medication_code] = $request->getPost("request");
                    $data->hcmedfor[$medication_code] = $bind[$medication_code];
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;
                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);

                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;
        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addIndicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["admission"] = $admission;

        $form = new IndicationForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;
        $request = $this->getRequest();


        if ($request->isGet())
            $model_data['form'] = $form;

        // Refactorizar ... (código repetido)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $indication = new Indication();

                if (isset($data->indicaciones))

                $model_data['indications'] = $data->indicaciones;

                else
                    $model_data['indications'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;

                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $indication = new Indication();
            $form->setValidationGroup('indicacion');
            $form->setInputFilter($indication->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $indication->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;
                    $model_data["form"] = new IndicationForm($this);
                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );


                    if (!isset($data['indicaciones']))
                        $data['indicaciones'] = array();
                    else
                        $data['indicaciones'] = (array) $data['indicaciones'];

                    $cod_ind = (count($data['indicaciones'])) ? max(array_keys($data['indicaciones'])) + 1 : 1;
                    $data["indicaciones"][$cod_ind] = $form->getData();
                    $data["indicaciones"][$cod_ind]["cod_ind"] = $cod_ind;
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                }
                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $indication = new Indication();

                if (isset($data->indicaciones))
                    $model_data['indications'] = $data->indicaciones;
                else
                    $model_data['indications'] = array();

            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);


        $view = new ViewModel($model_data);
        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;

    }

    public function viewIndicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['indicaciones']))
                $data['indicaciones'] = array();
            else
                $data['indicaciones'] = (array) $data['indicaciones'];
        }

        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'indications' => $data['indicaciones'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteIndicationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $id = (string) $this->params()->fromRoute('id', "");

            try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
                $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
                    throw new \Exception("No se ha podido cargar el buffer");
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['admission'] = null;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['indicaciones']))
                $data['indicaciones'] = array();
            else
                $data['indicaciones'] = (array) $data['indicaciones'];

            $indications = $this->getRequest()->getPost("request");
            $indicaciones = array();

            foreach ($data['indicaciones'] as $key => $value)
            {
                if (!in_array($key, $indications))
                    $indicaciones[$key] = $value;
            }

            $data['indicaciones'] = $indicaciones;
            $response = \Zend\Json\Json::encode( $data );

            try {
                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e)
            {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $indications ) ));

           return $response;
        }
    }

    public function editIndicationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $indication_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $indication_code = $this->getRequest()->getPost('request');
            $indication_code = $indication_code["cod_ind"];
        }
        else
            $indication_code = (string) $this->params()->fromRoute('type', "");


        $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new IndicationForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

            $indication = new Indication();

            if (isset($data->indicaciones))
                $model_data['indications'] = $data->indicaciones;
            else
                throw new \Exception("No existen indicaciones registradas");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $indicaciones = array();

        foreach ((array)$data->indicaciones as $key => $value)
        {
                $indicaciones[$key] = $value;
        }

        $bind = $data->indicaciones = $indicaciones;
        $indication->exchangeJson($bind[$indication_code]);
        $form->bind($indication);

        $form->get('submit')->setAttribute('value', 'Guardar');
        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup('indicacion');

            $form->setInputFilter($indication->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {

                    $bind[$indication_code] = $request->getPost("request");
                    $data->indicaciones[$indication_code] = $bind[$indication_code];
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;
                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);
                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;
        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addSolicitudeAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', "");
        $model_data["type"] = $type;

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->etMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["admission"] = $admission;
        $form = new SolicitudeForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        // Refactorizar ... (código repetido)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $medication = new Solicitude();
                if (isset($data->hcexapac) && count($data->hcexapac))
                {

                    $filter = (array) $data->hcexapac;
                    $_data = array();

                    foreach ($filter as $item) {
                    	if ($item->tip_exa == $type)
                    		$_data[] = $item;
                    }

                    $model_data['solicitudes'] = $_data;
                }
                else
                    $model_data['solicitudes'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $solicitude = new Solicitude();

            $form->setValidationGroup(
                'cod_exa', 'nom_exa', 'can_exa', 'est_exa', 'obs_exa'
            );

            $form->setInputFilter($solicitude->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $solicitude->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;
                    $model_data["form"] = new SolicitudeForm($this);
                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

                    if (!isset($data['hcexapac']))
                        $data['hcexapac'] = array();
                    else
                        $data['hcexapac'] = (array) $data['hcexapac'];

                    foreach ($data['hcexapac'] as $exa)
                    {
                        $gdata = $form->getData();

                        if ($exa->cod_exa == $gdata["cod_exa"])
                                throw new \Exception("No se puede ingresar dos veces el mismo exámen", 1);
                    }

                    $_data = $form->getData();
                    $_data["tip_exa"] = $type;

                    $data['hcexapac'][$solicitude->cod_exa] = $_data;

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);
                }
                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $medication = new Solicitude();

                if (isset($data->hcexapac) && count($data->hcexapac))
                {
                    $filter = (array) $data->hcexapac;

                    $_data = array();

                    foreach ($filter as $item)
                    {
                    	if ($item->tip_exa == $type)
                    		$_data[] = $item;
                    }

                    $model_data['solicitudes'] = $_data;
                }
                else
                    $model_data['solicitudes'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);
                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function searchSolicitudeAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $term = (string) $_GET["term"];         // BUG [Debería utilizarse from->route pero no funciona]

		$type = (string) $this->params()->fromRoute('type', "");

        try {

            $exam = $this->getExamsTable()->search($term, array("limit" => 30, "type" => $type));
            $exams = $exam->toArray();
            $data = array();

            foreach ($exams as $exam)
            {
                $data[] = array(
                    "id" => $exam["cod_exa"],
                    "value" => $exam["cod_exa"]." - ".$exam["nom_exa"],
                );
            }
        }

        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();

            $data = array(
                0 => array(
                    "id" => "error",
                    "value" => $e->getMessage()
                )
            );

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));
            return $response;
        }

        $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

        return $response;
    }

    public function viewSolicitudesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (isset($data['hcexapac']) && count($data['hcexapac']))
            {
                $filter = $data['hcexapac'];

                $_data = array();

                foreach ($filter as $item)
                {
                	if ($item->tip_exa == $type)
                		$_data[] = $item;
                }

                $model_data['hcexapac'] = $_data;
            }
            else
                $model_data['hcexapac'] = array();
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'type' => $type,
            'solicitudes' => $model_data['hcexapac'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteSolicitudesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {

            $id = (string) $this->params()->fromRoute('id', "");
            $type = (string) $this->params()->fromRoute('type', "");

            try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
                $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
                    throw new \Exception("No se ha podido cargar el buffer");
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['admission'] = null;

                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['hcexapac']))
                $data['hcexapac'] = array();
            else
                $data['hcexapac'] = (array) $data['hcexapac'];


            $solicitudes = $this->getRequest()->getPost("request");
            $hcexapac = array();

            foreach ($data['hcexapac'] as $key => $value)
            {
                if (!in_array($key, $solicitudes))
                    $hcexapac[$key] = $value;
            }

            $data['hcexapac'] = $hcexapac;
            $response = \Zend\Json\Json::encode( $data );

            try {

                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e) {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $solicitudes ) ));

            return $response;
        }
    }

    public function editSolicitudeAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $exam_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $exam_code = $this->getRequest()->getPost('request');
            $exam_code = $exam_code["cod_exa"];
        }
        else
            $exam_code = (string) $this->params()->fromRoute('type', "");

        $id = (string) $this->params()->fromRoute('id', "");
        $type = (string) $this->params()->fromRoute('type', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;
        $model_data['type'] = $type;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new SolicitudeForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
            $exam = new Solicitude();

            if (isset($data->hcexapac))
                $model_data['exams'] = $data->hcexapac;
            else
                throw new \Exception("No existen solicitudes registrados");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $hcexapac = array();

        foreach ((array)$data->hcexapac as $key => $value)
        {
            $hcexapac[$key] = $value;
        }

        $bind = $data->hcexapac = $hcexapac;

        $exam->exchangeJson($bind[$exam_code]);
        $form->bind($exam);

        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_exa', 'nom_exa', 'tip_exa', 'can_exa', 'est_exa', 'obs_exa'
            );

            $form->setInputFilter($exam->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {

                    $bind[$exam_code] = $request->getPost("request");
                    $data->hcexapac[$exam_code] = $bind[$exam_code];
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;
                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);

                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addInterconsultationAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["admission"] = $admission;

        $form = new InterconsultationForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;

        // Refactorizar ... (código repetido)
        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $medication = new Interconsultation();

                if (isset($data->interconsultas))
                    $model_data['interconsultations'] = $data->interconsultas;
                else
                    $model_data['interconsultations'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $interconsultation = new Interconsultation();

            $form->setValidationGroup(
                'cod_dia', 'nom_dia', 'cod_esp', 'nom_esp', 'mot_int'
            );

            $form->setInputFilter($interconsultation->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $interconsultation->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;
                    $model_data["form"] = new interconsultationForm($this);

                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

                    if (!isset($data['interconsultas']))
                        $data['interconsultas'] = array();
                    else
                        $data['interconsultas'] = (array) $data['interconsultas'];

                    foreach ($data['interconsultas'] as $int)
                    {
                        $gdata = $form->getData();

                        if ($int->cod_dia == $gdata["cod_dia"])
                                throw new \Exception("No se puede ingresar dos interconsultas con el mismo diagnóstico", 1);
                    }

                    $cod_int = (count($data['interconsultas'])) ? max(array_keys($data['interconsultas'])) + 1 : 1;
                    $data["interconsultas"][$cod_int] = $form->getData();
                    $data["interconsultas"][$cod_int]["cod_int"] = $cod_int;

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                }
                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)

        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

                $interconsultation = new Interconsultation();

                if (isset($data->interconsultas))
                    $model_data['interconsultations'] = $data->interconsultas;
                else
                    $model_data['interconsultations'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewInterconsultationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['interconsultas']))
                $data['interconsultas'] = array();
            else
                $data['interconsultas'] = (array) $data['interconsultas'];

        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'interconsultations' => $data['interconsultas'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function searchSpecialtyAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $term = (string) $_GET["term"];         // BUG [Debería utilizarse from->route pero no funciona]

        try {

            $specialty = $this->getSpecialtyTable()->search($term, array("limit" => 30));

            $specialties = $specialty->toArray();

            $data = array();

            foreach ($specialties as $specialty)
            {
                $data[] = array(
                    "id" => $specialty["cod_esp"],
                    "value" => $specialty["cod_esp"]." - ".$specialty["nom_esp"],
                );
            }
        }

        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();

            $data = array(
                0 => array(
                    "id" => "error",
                    "value" => $e->getMessage()
                )
            );

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

            return $response;
        }

        $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( $data ));

        return $response;
    }

    public function deleteInterconsultationsAction()
    {
        $this->authenticate();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $id = (string) $this->params()->fromRoute('id', "");

            try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
                $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
                    throw new \Exception("No se ha podido cargar el buffer");

            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['admission'] = null;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['interconsultas']))
                $data['interconsultas'] = array();
            else
                $data['interconsultas'] = (array) $data['interconsultas'];

            $medications = $this->getRequest()->getPost("request");

            $interconsultas = array();

            foreach ($data['interconsultas'] as $key => $value)
            {
                if (!in_array($key, $medications))
                    $interconsultas[$key] = $value;
            }

            $data['interconsultas'] = $interconsultas;

            $response = \Zend\Json\Json::encode( $data );

            try {
                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e) {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $medications ) ));

            return $response;
        }
    }

    public function editInterconsultationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $interconsultation_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $interconsultation_code = $this->getRequest()->getPost('request');
            $interconsultation_code = $interconsultation_code["cod_int"];

        }
        else
            $interconsultation_code = (string) $this->params()->fromRoute('type', "");

        $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new InterconsultationForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
            $interconsultation = new Interconsultation();

            if (isset($data->interconsultas))
                $model_data['interconsultations'] = $data->interconsultas;
            else
                throw new \Exception("No existen interconsultas registrados");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $interconsultas = array();

        foreach ((array)$data->interconsultas as $key => $value)
        {
                $interconsultas[$key] = $value;
        }

        $bind = $data->interconsultas = $interconsultas;
        $interconsultation->exchangeJson($bind[$interconsultation_code]);
        $form->bind($interconsultation);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_dia', 'nom_dia', 'cod_esp', 'nom_esp', 'mot_int'
            );

            $form->setInputFilter($interconsultation->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {

                    $bind[$interconsultation_code] = $request->getPost("request");

                    $data->interconsultas[$interconsultation_code] = $bind[$interconsultation_code];

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;
                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;

                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);

                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addIncapacityAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $model_data["admission"] = $admission;

        $form = new IncapacityForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isGet())
            $model_data['form'] = $form;


        // Refactorizar ... (código repetido)

        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $incapacity = new Incapacity();

                if (isset($data->incapacidad))
                    $model_data['incapacities'] = $data->incapacidad;
                else
                    $model_data['incapacities'] = array();
            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        if ($request->isPost())
        {
            $medication = new Incapacity();

            $form->setValidationGroup(
                'cod_dia', 'nom_dia', 'num_dia_inc', 'fec_ini_inc', 'des_inc'
            );

            $form->setInputFilter($medication->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $medication->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;
                    $model_data["form"] = new IncapacityForm($this);

                    $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

                    if (!isset($data['incapacidad']))
                        $data['incapacidad'] = array();
                    else
                        $data['incapacidad'] = (array) $data['incapacidad'];

                    if (count($data["incapacidad"]))
                        throw new \Exception("No se pueden crear dos incapacidades por folio", 1);

                    $data['incapacidad'][$medication->cod_dia] = $form->getData();

                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);
                }

                catch (\Exception $e)
                {
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

        // No se repetiría desde la versión 5.5 de PHP (soporte para finally)

        if (file_exists($buffer))
        {
            try {

                $data = \Zend\Json\Json::decode( file_get_contents($buffer) );
                $incapacity = new Incapacity();

                if (isset($data->incapacidad))
                    $model_data['incapacities'] = $data->incapacidad;
                else
                    $model_data['incapacities'] = array();

            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['form'] = $form;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewIncapacitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);

            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

            if (!file_exists($buffer))
                throw new \Exception("No se ha podido cargar el buffer");

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['incapacidades']))
                $data['incapacidades'] = array();
            else
                $data['incapacidades'] = (array) $data['incapacidades'];
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'incapacities' => $data['incapacidad'],
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteIncapacitiesAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $id = (string) $this->params()->fromRoute('id', "");

            try {

                $admission = $this->getAdmissionTable()->getFullAdmission($id);
                $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";

                if (!file_exists($buffer))
                    throw new \Exception("No se ha podido cargar el buffer");

            }
            catch (\Exception $e)
            {
                $model_data['Exception'] = $e->getMessage();
                $model_data['admission'] = null;
                $view = new ViewModel($model_data);

                if ($xmlHttpRequest)
                    $view->setTerminal(true);

                return $view;
            }

            $data = (array) \Zend\Json\Json::decode( file_get_contents($buffer) );

            if (!isset($data['incapacidad']))
                $data['incapacidad'] = array();
            else
                $data['incapacidad'] = (array) $data['incapacidad'];

            $incapacities = $this->getRequest()->getPost("request");
            $incapacidad = array();

            foreach ($data['incapacidad'] as $key => $value)
            {
                if (!in_array($key, $incapacities))
                    $incapacidad[$key] = $value;
            }

            $data['incapacidad'] = $incapacidad;

            $response = \Zend\Json\Json::encode( $data );

            try {

                if (file_put_contents($buffer, $response) === false)
                    throw new \Exception("Error de procesamiento del buffer interno", 1);
            }
            catch (\Exception $e)
            {
                echo $e->getMessage();
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $incapacities ) ));

            return $response;
        }
    }

    public function editIncapacityAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $incapacity_code = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $incapacity_code = $this->getRequest()->getPost('request');
            $incapacity_code = $incapacity_code["cod_dia"];
        }
        else
            $incapacity_code = (string) $this->params()->fromRoute('type', "");

        $id = (string) $this->params()->fromRoute('id', "");

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        $model_data['id'] = $id;

        try {

            $admission = $this->getAdmissionTable()->getAdmission($id);
            $buffer = "cache/".$admission->cod_adm."_".$this->getIdentity()->cod_usu."_".$admission->num_doc_pac."_".$admission->cod_tip_doc.".json";
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new IncapacityForm($this);

        try {

            $data = \Zend\Json\Json::decode( file_get_contents($buffer) );

            $incapacity = new Incapacity();

            if (isset($data->incapacidad))
                $model_data['incapacities'] = $data->incapacidad;
            else
                throw new \Exception("No existen medicamentos registrados");
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['form'] = $form;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $incapacidad = array();

        foreach ((array)$data->incapacidad as $key => $value)
        {
                $incapacidad[$key] = $value;
        }

        $bind = $data->incapacidad = $incapacidad;
        $incapacity->exchangeJson($bind[$incapacity_code]);

        $form->bind($incapacity);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_dia', 'nom_dia', 'fec_ini_inc', 'num_dia_inc', 'des_inc'
            );

            $form->setInputFilter($incapacity->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {
                    $bind[$incapacity_code] = $request->getPost("request");
                    $data->incapacidad[$incapacity_code] = $bind[$incapacity_code];
                    $response = \Zend\Json\Json::encode( $data );

                    if (file_put_contents($buffer, $response) === false)
                        throw new \Exception("Error de procesamiento del buffer interno", 1);

                    $model_data['Success'] = true;
                }
                catch (\Exception $e)
                {
                    $model_data['Exception'] = $e->getMessage();
                    $model_data['form'] = $form;
                    $view = new ViewModel($model_data);

                    if ($xmlHttpRequest)
                        $view->setTerminal(true);

                    return $view;
                }
            }
        }

        $model_data['form'] = $form;
        $model_data['admission'] = $admission;
        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function addCertificationAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        $adm = (string) $this->params()->fromRoute('type', "");


        $model_data["admission"] = $admission;

        $form = new CertificationForm($this);

        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isGet())
        {
            $model_data['form'] = $form;

            $certification = new Certification();

            $certification->exchangeArray(array(
                "cod_adm" => $id,
                "cod_tip_doc" => $admission->cod_tip_doc,
                "num_doc_pac" => $admission->num_doc_pac,
                "cod_usu" => $admission->cod_usu_med,
                "usu_med" => $admission->usu_med,
            ));

            $form->bind($certification);
            $form->get('submit')->setAttribute('value', 'Guardar');
        }

        if ($request->isPost())
        {
            $form->setValidationGroup(
                'cod_adm', 'cod_tip_doc', 'num_doc_pac', 'fec_reg', 'cod_usu', 'tit_cer', 'des_cer'
            );

            $certification = new Certification();
            $form->setInputFilter($certification->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $certification->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;

                    $this->getCertificationTable()->addCertification($certification);
                    $model_data['form'] = $form;

                    $certification = new Certification();

                    $certification->exchangeArray(array(
                        "cod_adm" => $id,
                        "cod_tip_doc" => $admission->cod_tip_doc,
                        "num_doc_pac" => $admission->num_doc_pac,
                        "cod_usu" => $admission->cod_usu_med,
                        "usu_med" => $admission->usu_med,
                    ));

                    $form->bind($certification);
                }
                catch (\Exception $e)
                {
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

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);
        $model_data["certifications"] = $this->getCertificationTable()->getPatientCertifications($admission->num_doc_pac, $admission->cod_tip_doc, $admission->cod_usu_med);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewCertificationsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {

            $admission = $this->getAdmissionTable()->getFullAdmission($id);
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;

            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'certifications' => $this->getCertificationTable()->getPatientCertifications($admission->num_doc_pac, $admission->cod_tip_doc, $admission->cod_usu_med),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteCertificationsAction()
    {
        $this->authenticate();

        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $certifications = $this->getRequest()->getPost("request");

            foreach ($certifications as $certification)
            {
                $this->getCertificationTable()->deleteCertification($certification);
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $certifications ) ));

            return $response;
        }
    }

    public function editCertificationAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $id = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_cer"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', '');

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        try {

            $certification = $this->getCertificationTable()->getCertification($id);
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new CertificationForm($this);
        $form->bind($certification);
        $form->get('submit')->setAttribute('value', 'Guardar');


        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_cer', 'tit_cer', 'des_cer', 'cod_adm', 'cod_tip_doc', 'num_doc_pac', 'fec_reg', 'cod_usu'
            );

            $form->setInputFilter($certification->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));


            if ($form->isValid())
            {
                try {
                    $this->getCertificationTable()->updateCertification($certification);
                    $model_data['Success'] = true;
                }

                catch (\Exception $e)
                {
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

    public function addBackgroundAction()
    {
        $this->authenticate();

        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {
            $admission = $this->getAdmissionTable()->getFullAdmission($id);
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $id = (string) $this->params()->fromRoute('id', "");
        $adm = (string) $this->params()->fromRoute('type', "");

        $model_data["admission"] = $admission;

        $form = new BackgroundForm($this);
        $model_data["open"] = (empty($admission->est_adm)) ? true : false;
        $model_data["xmlHttpRequest"] = $xmlHttpRequest;

        $request = $this->getRequest();

        if ($request->isGet())
        {
            $model_data['form'] = $form;
            $background = new Background();

            $background->exchangeArray(array(
                "cod_tip_doc" => $admission->cod_tip_doc,
                "num_doc_pac" => $admission->num_doc_pac,
                "usu_reg" => $admission->cod_usu_med,
            ));

            $form->bind($background);
            $form->get('submit')->setAttribute('value', 'Guardar');
        }

        $model_data["admission"] = $this->getAdmissionTable()->getFullAdmission($id);
        $model_data["backgrounds"] = $this->getBackgroundTable()->getPatientBackgrounds($admission->num_doc_pac, $admission->cod_tip_doc, $this->getIdentity()->cod_usu);

        if ($request->isPost())
        {

            $form->setValidationGroup(
                'cod_tip_doc', 'num_doc_pac', 'tip_ant', 'des_ant', 'usu_reg'
            );

            $background = new Background();
            $form->setInputFilter($background->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $background->exchangeArray($form->getData());

                try {

                    $model_data['Success'] = true;

                    $this->getBackgroundTable()->addBackground($background);

                    $model_data['form'] = $form;

                    $background = new Background();

                    $background->exchangeArray(array(
                        "cod_tip_doc" => $admission->cod_tip_doc,
                        "num_doc_pac" => $admission->num_doc_pac,
                        "usu_reg" => $admission->cod_usu_med,
                    ));

                    $form->bind($background);
                }
                catch (\Exception $e)
                {
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

        $model_data["backgrounds"] = $this->getBackgroundTable()->getPatientBackgrounds($admission->num_doc_pac, $admission->cod_tip_doc, $this->getIdentity()->cod_usu);

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function viewBackgroundsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $id = (string) $this->params()->fromRoute('id', "");

        try {
            $admission = $this->getAdmissionTable()->getFullAdmission($id);
        }

        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $model_data['admission'] = null;
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $view = new ViewModel(array(
            'admission' => $admission,
            'backgrounds' => $this->getBackgroundTable()->getPatientBackgrounds($admission->num_doc_pac, $admission->cod_tip_doc),
            'xmlHttpRequest' => $xmlHttpRequest,
            'simulateXmlHttpRequest' => true
        ));

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }

    public function deleteBackgroundsAction()
    {
        $this->authenticate();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        if ($xmlHttpRequest)
        {
            $backgrounds = $this->getRequest()->getPost("request");

            foreach ($backgrounds as $background) {
                $this->getBackgroundTable()->deleteBackground($background);
            }

            $response = $this->getResponse()->setContent(\Zend\Json\Json::encode( array( "response" => $backgrounds ) ));

            return $response;
        }
    }

    public function editBackgroundAction()
    {
        $this->authenticate();
        $model_data = array();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();

        $action = $this->getRequest()->getPost('action');

        if ($xmlHttpRequest && $action == "get")
            $id = $this->getRequest()->getPost('request');
        elseif ($xmlHttpRequest && $action == "edit")
        {
            $id = $this->getRequest()->getPost('request');
            $id = $id["cod_ant"];
        }
        else
            $id = (string) $this->params()->fromRoute('id', '');

        if (empty($id))
            return $this->redirect()->toRoute('MedicalHistory', array(
                'action' => 'index'
            ));

        try {

            $background = $this->getBackgroundTable()->getBackground($id);
        }
        catch (\Exception $e)
        {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);

            if ($xmlHttpRequest)
                $view->setTerminal(true);

            return $view;
        }

        $form  = new BackgroundForm($this);
        $form->bind($background);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();

        if (($request->isPost() && !$xmlHttpRequest) || ($xmlHttpRequest && $action == "edit" ))
        {
            $form->setValidationGroup(
                'cod_ant', 'cod_tip_doc', 'num_doc_pac', 'tip_ant', 'des_ant', 'usu_reg'
            );

            $form->setInputFilter($background->getInputFilter());

            if (!$xmlHttpRequest)
                $form->setData($request->getPost());
            else
                $form->setData($request->getPost("request"));

            if ($form->isValid())
            {
                try {

                    $this->getBackgroundTable()->updateBackground($background);
                    $model_data['Success'] = true;

                }
                catch (\Exception $e)
                {
                    return array(
                        'id' => $id,
                        'form' => $form,
                        'Exception' => $e->getMessage(),
                    );
                }
            }
            else
            	echo "not valid";
        }

        $model_data['id'] = $id;
        $model_data['form'] = $form;
        $model_data['xmlHttpRequest'] = $xmlHttpRequest;

        $view = new ViewModel($model_data);

        if ($xmlHttpRequest)
            $view->setTerminal(true);

        return $view;
    }
}