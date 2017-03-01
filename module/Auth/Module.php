<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Auth;

use Auth\Model\Entity\User;
use Auth\Model\Entity\UserTable;
use Auth\Model\Entity\LogIngress;
use Auth\Model\Entity\LogIngressTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Auth\Model\DbAdapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

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
				'Auth\Model\Entity\UserTable' =>  function($sm) {
					$tableGateway = $sm->get('UserTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new UserTable($tableGateway, $dbAdapter);
					return $table;
				},
				'UserTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new User());
					return new TableGateway('hcneuro_usuarios', $dbAdapter, null, $resultSetPrototype);
				},
				'AuthAdapter' => function ($sm) {
					$DbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$authAdapter = new AuthAdapter($DbAdapter);
					$authAdapter
						->setTableName('hcneuro_usuarios')
						->setIdentityColumn('cod_usu')
						->setCredentialColumn('pas_usu')
					;
					return $authAdapter;
				},
				'Auth\Model\Entity\LogIngressTable' =>  function($sm) {
					$tableGateway = $sm->get('LogIngressTableGateway');
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$table = new LogIngressTable($tableGateway, $dbAdapter);
					return $table;
				},
				'LogIngressTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new LogIngress());
					return new TableGateway('log_ingresos', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}
}
