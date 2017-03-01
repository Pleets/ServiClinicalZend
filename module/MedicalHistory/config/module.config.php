<?php

return array(
	'controllers' => array(
		'invokables' => array(
			'MedicalHistory\Controller\Manage' => 'MedicalHistory\Controller\ManageController',
		),
	),
	'router' => array(
		'routes' => array(
			'medicalHistory' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/medicalHistory[[/:action][/:id][/:type]]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[a-zA-Z0-9_-]*',
						'type' => '[a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'controller' => 'MedicalHistory\Controller\Manage',
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
            'medicalHistory' => __DIR__ . '/../view',
        ),
    ),
);
