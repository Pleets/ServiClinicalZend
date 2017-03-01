<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Admissions;

use Admissions\Model\Entity\Patient;
use Admissions\Model\Entity\PatientsTable;
use Admissions\Model\Entity\Admission;
use Admissions\Model\Entity\AdmissionsTable;
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
			),
		);
	}
}
