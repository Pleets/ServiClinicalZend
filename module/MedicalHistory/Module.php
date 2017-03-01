<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory;

use Admissions\Model\Entity\Patient;
use Admissions\Model\Entity\PatientsTable;
use Admissions\Model\Entity\Admission;
use Admissions\Model\Entity\AdmissionsTable;
use MedicalHistory\Model\Entity\Diagnostic;
use MedicalHistory\Model\Entity\DiagnosticTable;
use MedicalHistory\Model\Entity\Certification;
use MedicalHistory\Model\Entity\CertificationTable;
use MedicalHistory\Model\Entity\Specialty;
use MedicalHistory\Model\Entity\SpecialtyTable;
use MedicalHistory\Model\Entity\Ingress;
use MedicalHistory\Model\Entity\IngressTable;
use MedicalHistory\Model\Entity\Adjuntos;
use MedicalHistory\Model\Entity\AdjuntosTable;
use MedicalHistory\Model\Entity\PatientDiagnostic;
use MedicalHistory\Model\Entity\PatientDiagnosticTable;
use MedicalHistory\Model\Entity\PatientMedication;
use MedicalHistory\Model\Entity\PatientMedicationTable;
use MedicalHistory\Model\Entity\PatientExam;
use MedicalHistory\Model\Entity\PatientExamTable;
use MedicalHistory\Model\Entity\Incapacity;
use MedicalHistory\Model\Entity\IncapacityTable;
use MedicalHistory\Model\Entity\Indication;
use MedicalHistory\Model\Entity\IndicationTable;
use MedicalHistory\Model\Entity\Interconsultation;
use MedicalHistory\Model\Entity\InterconsultationTable;
use MedicalHistory\Model\Entity\TypeHistory;
use MedicalHistory\Model\Entity\TypeHistoryTable;
use MedicalHistory\Model\Entity\FirstHistory;
use MedicalHistory\Model\Entity\FirstHistoryTable;
use MedicalHistory\Model\Entity\ControlHistory;
use MedicalHistory\Model\Entity\ControlHistoryTable;
use MedicalHistory\Model\Entity\Observation;
use MedicalHistory\Model\Entity\ObservationTable;

use MedicalHistory\Model\Entity\Patologia;
use MedicalHistory\Model\Entity\PatologiaTable;

use MedicalHistory\Model\Entity\Liquidos;
use MedicalHistory\Model\Entity\LiquidosTable;


use MedicalHistory\Model\Entity\Citologia;
use MedicalHistory\Model\Entity\CitologiaTable;

use MedicalHistory\Model\Entity\Background;
use MedicalHistory\Model\Entity\BackgroundTable;
use Settings\Model\Entity\Medication;
use Settings\Model\Entity\MedicationsTable;
use Settings\Model\Entity\Exam;
use Settings\Model\Entity\ExamsTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Auth\Model\DbAdapter;

class Module
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Admissions\Model\Entity\PatientTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new PatientsTable($tableGateway, $dbAdapter);
					return $table;
				},
				'PatientTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Patient());
					return new TableGateway('hcneuro_pacientes', $dbAdapter, null, $resultSetPrototype);
				},
				'Admissions\Model\Entity\AdmissionTable' =>  function($sm) {
					$tableGateway = $sm->get('AdmissionTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new AdmissionsTable($tableGateway, $dbAdapter);
					return $table;
				},
				'AdmissionTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Admission());
					return new TableGateway('hcneuro_admision', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\DiagnosticTable' =>  function($sm) {
					$tableGateway = $sm->get('DiagnosticTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new DiagnosticTable($tableGateway, $dbAdapter);
					return $table;
				},
				'DiagnosticTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Diagnostic());
					return new TableGateway('hcneuro_cie10', $dbAdapter, null, $resultSetPrototype);
				},
				'Settings\Model\Entity\MedicationsTable' =>  function($sm) {
					$tableGateway = $sm->get('MedicationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new MedicationsTable($tableGateway, $dbAdapter);
					return $table;
				},
				'MedicationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Medication());
					return new TableGateway('hcneuro_medicamentos', $dbAdapter, null, $resultSetPrototype);
				},
				'Settings\Model\Entity\ExamsTable' =>  function($sm) {
					$tableGateway = $sm->get('ExamTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new ExamsTable($tableGateway, $dbAdapter);
					return $table;
				},
				'ExamTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Exam());
					return new TableGateway('hcneuro_examenes', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\SpecialtyTable' =>  function($sm) {
					$tableGateway = $sm->get('SpecialtyTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new SpecialtyTable($tableGateway, $dbAdapter);
					return $table;
				},
				'SpecialtyTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Specialty());
					return new TableGateway('hcneuro_especialidades', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\CertificationTable' =>  function($sm) {
					$tableGateway = $sm->get('CertificationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new CertificationTable($tableGateway, $dbAdapter);
					return $table;
				},
				'CertificationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Certification());
					return new TableGateway('hcneuro_certificacion', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\IngressTable' =>  function($sm) {
					$tableGateway = $sm->get('IngressTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new IngressTable($tableGateway, $dbAdapter);
					return $table;
				},
				'IngressTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Ingress());
					return new TableGateway('hcneuro_hcingresos', $dbAdapter, null, $resultSetPrototype);
				},
				'PatientDiagnosticTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new PatientDiagnostic());
					return new TableGateway('hcneuro_hcdiapac', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\PatientDiagnosticTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientDiagnosticTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new PatientDiagnosticTable($tableGateway, $dbAdapter);
					return $table;
				},
				'PatientMedicationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new PatientMedication());
					return new TableGateway('hcneuro_hcmedfor', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\PatientMedicationTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientMedicationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new PatientMedicationTable($tableGateway, $dbAdapter);
					return $table;
				},
				'IndicationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Indication());
					return new TableGateway('hcneuro_indicaciones', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\IndicationTable' =>  function($sm) {
					$tableGateway = $sm->get('IndicationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new IndicationTable($tableGateway, $dbAdapter);
					return $table;
				},
				'PatientExamTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new PatientExam());
					return new TableGateway('hcneuro_hcexapac', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\PatientExamTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientExamTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new PatientExamTable($tableGateway, $dbAdapter);
					return $table;
				},
				'InterconsultationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Interconsultation());
					return new TableGateway('hcneuro_interconsultas', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\InterconsultationTable' =>  function($sm) {
					$tableGateway = $sm->get('InterconsultationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new InterconsultationTable($tableGateway, $dbAdapter);
					return $table;
				},
				'IncapacityTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Incapacity());
					return new TableGateway('hcneuro_incapacidad', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\IncapacityTable' =>  function($sm) {
					$tableGateway = $sm->get('IncapacityTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new IncapacityTable($tableGateway, $dbAdapter);
					return $table;
				},
				'TypeHistoryTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new TypeHistory());
					return new TableGateway('hcneuro_tipohis', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\TypeHistoryTable' =>  function($sm) {
					$tableGateway = $sm->get('TypeHistoryTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new TypeHistoryTable($tableGateway, $dbAdapter);
					return $table;
				},
				'FirstHistoryTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new FirstHistory());
					return new TableGateway('hcneuro_histo_primera', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\FirstHistoryTable' =>  function($sm) {
					$tableGateway = $sm->get('FirstHistoryTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new FirstHistoryTable($tableGateway, $dbAdapter);
					return $table;
				},
				'ControlHistoryTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new ControlHistory());
					return new TableGateway('hcneuro_histo_control', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\ControlHistoryTable' =>  function($sm) {
					$tableGateway = $sm->get('ControlHistoryTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new ControlHistoryTable($tableGateway, $dbAdapter);
					return $table;
				},
				'ObservationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Observation());
					return new TableGateway('hcneuro_histo_obser', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\ObservationTable' =>  function($sm) {
					$tableGateway = $sm->get('ObservationTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new ObservationTable($tableGateway, $dbAdapter);
					return $table;
				},

                'PatologiaTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Patologia());
					return new TableGateway('hcneuro_patologia', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\PatologiaTable' =>  function($sm) {
					$tableGateway = $sm->get('PatologiaTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new PatologiaTable($tableGateway, $dbAdapter);
					return $table;
				},
                 'LiquidosTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Liquidos());
					return new TableGateway('hcneuro_liquidos', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\LiquidosTable' =>  function($sm) {
					$tableGateway = $sm->get('LiquidosTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new LiquidosTable($tableGateway, $dbAdapter);
					return $table;
				},

				'CitologiaTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Citologia());
					return new TableGateway('hcneuro_citologia', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\CitologiaTable' =>  function($sm) {
					$tableGateway = $sm->get('CitologiaTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new CitologiaTable($tableGateway, $dbAdapter);
					return $table;
				},

				'AdjuntosTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Adjuntos());
					return new TableGateway('hcneuro_adjuntos', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\AdjuntosTable' =>  function($sm) {
					$tableGateway = $sm->get('AdjuntosTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new AdjuntosTable($tableGateway, $dbAdapter);
					return $table;
				},


				'BackgroundTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Background());
					return new TableGateway('hcneuro_antecedentes', $dbAdapter, null, $resultSetPrototype);
				},
				'MedicalHistory\Model\Entity\BackgroundTable' =>  function($sm) {
					$tableGateway = $sm->get('BackgroundTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new BackgroundTable($tableGateway, $dbAdapter);
					return $table;
				},
			),
		);
	}
}
