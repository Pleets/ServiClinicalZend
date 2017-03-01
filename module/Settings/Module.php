<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Settings;

use Settings\Model\Entity\Exam;
use Settings\Model\Entity\ExamsTable;
use Settings\Model\Entity\Entity;
use Settings\Model\Entity\EntitiesTable;
use Settings\Model\Entity\Medication;
use Settings\Model\Entity\MedicationsTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
				'Settings\Model\Entity\EntitiesTable' =>  function($sm) {
					$tableGateway = $sm->get('EntityTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new EntitiesTable($tableGateway, $dbAdapter);
					return $table;
				},
				'EntityTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Entity());
					return new TableGateway('hcneuro_entidad', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}

}
