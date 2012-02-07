<?php
/**
 * Set path to Zend Framework project and start.
 */
chdir(dirname(__DIR__));
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') 
    . '/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array()
    ));

/**
 * Set path to project configuration files.
 */
$appConfig = include 'config/application.config.php';

/**
 * Set View listeners and configure.
 */
$listenerOptions  = 
    new Zend\Module\Listener\ListenerOptions(
            $appConfig['module_listener_options']
            );
$defaultListeners = 
    new Zend\Module\Listener\DefaultListenerAggregate(
            $listenerOptions
            );
$defaultListeners
    ->getConfigListener()
    ->addConfigGlobPath('config/autoload/*.config.php');
/**
 * Load up the Module Manager and use it to set up project modules.
 */
$moduleManager = 
    new Zend\Module\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
/*
echo "<pre>";
print_r($moduleManager);
echo "</pre>";
 * 
 */
$moduleManager->loadModules();

/**
 * With everything necessary for the project now in place, bootstrap it,
 * create the application from now loaded bootstrap credentials, and run.
 */
$bootstrap   = 
    new Zend\Mvc\Bootstrap($defaultListeners
            ->getConfigListener()
            ->getMergedConfig()
            );

$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);
$application->run()->send();