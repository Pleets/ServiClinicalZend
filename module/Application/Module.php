<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\I18n\Translator as Translate;
use Zend\I18n\Translator\Translator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translate = new Translator();
        $parseTranslator = new Translate($translate);
        $parseTranslator->addTranslationFile(
            'phpArray',
            './vendor/zendframework/zendframework/resources/languages/es/Zend_Validate.php',
            'default',
            'es_ES'
        );
        $parseTranslator->setLocale('es_ES');

        \Zend\Validator\AbstractValidator::setDefaultTranslator($parseTranslator);

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
