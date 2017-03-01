<?php

return array(
	'controllers' => array(
		'invokables' => array(
			'Settings\Controller\Manage' => 'Settings\Controller\ManageController',
		),
	),
	'router' => array(
		'routes' => array(
			'settings' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/settings[[/:action][/:id]]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[a-zA-Z0-9_-][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'controller' => 'Settings\Controller\Manage',
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
			'settings' => __DIR__ . '/../view',
		),
	),
);
