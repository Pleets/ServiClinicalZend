<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
	'db' => array(
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=serviclinical;host=localhost',
		'driver_options' => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		),
	),
	'service_manager' => array(
		'factories' => array(
			'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		),
        'invokables' => array(
            'authenticationService' => 'Zend\Authentication\AuthenticationService',
            'ZendAcl' => 'Zend\Permissions\Acl\Acl',
            'AclAdapter' => 'Auth\Model\AclAdapter',
        ),
	),
	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Autenticación',
				'route' => 'auth',
				'pages' => array(
					array(
						'label' => 'Usuarios',
						'route' => 'auth',
						'action' => 'view-users',
					),
					array(
						'label' => 'Permisos',
						'route'=> 'auth',
						'action' => 'permissions',
					),
				),
			),
			array(
				'label' => 'Admisiones',
				'route' => 'admissions',
				'pages' => array(
					array(
						'label' => 'Pacientes',
						'route' => 'admissions',
						'action' => 'view-patients',
					),
					array(
						'label' => 'Admisiones',
						'route'=> 'admissions',
						'action' => 'view-admissions',
					),
				),
			),
			array(
				'label' => 'Configuración',
				'route' => 'settings',
				'pages' => array(
					array(
						'label' => 'Exámenes',
						'route' => 'settings',
						'action' => 'view-exams',
					),
					array(
						'label' => 'Medicamentos',
						'route'=> 'settings',
						'action' => 'view-medications',
					),
					array(
						'label' => 'Entidades',
						'route'=> 'settings',
						'action' => 'view-entities',
					),
				),
			),
			array(
				'label' => 'Historia Clínica',
				'route' => 'medicalHistory',
				'pages' => array(
					array(
						'label' => 'Inicio',
						'route' => 'medicalHistory',
						'action' => 'index'
					),
				),
			),
			array(
				'label' => '© Neuro | Msoft',
				'route' => 'application',
				'pages' => array(
					array(
						'label' => 'Desarrollo',
						'route' => 'application',
						'action' => 'development'
					),
				),
			),
		),
	),
);
