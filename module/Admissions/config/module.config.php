<?php

return array(
	'controllers' => array(
		'invokables' => array(
			'Admissions\Controller\Manage' => 'Admissions\Controller\ManageController',
		),
	),
	'router' => array(
		'routes' => array(
			'admissions' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/admissions[[/:action][/:id][/:type]]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[a-zA-Z0-9_-]*',
						'type' => '[1-8]',
					),
					'defaults' => array(
						'controller' => 'Admissions\Controller\Manage',
						'action' => 'index',
					),
				),
			),
		),
	),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'es_ES',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
	'view_manager' => array(
		'template_path_stack' => array(
			'admissions' => __DIR__ . '/../view',
		),
	),
);
