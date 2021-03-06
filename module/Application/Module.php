<?php

namespace Application;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
    protected $view;
    protected $viewListener;

    public function init(Manager $moduleManager)
    {
        $events = StaticEventManager::getInstance();

        $events->attach('bootstrap', 'bootstrap', array(
            $this,
            'initializeView'),
            100);
    }
    
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
    
    public function initializeView($e)
    {
/*
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('config');
        $view         = $this->getView($app);
        $viewListener = $this->getViewListener($view, $config);
        $app->events()->attachAggregate($viewListener);
        $events       = StaticEventManager::getInstance();
        $viewListener->registerStaticListeners($events, $locator);
*/        

        // Get the DI object
        $locator      = $e->getParam('application')->getLocator();
        // Get the config object
        $config       = $e->getParam('config');
        // Get the \View\PhpRenderer object (see method in this class)
        $view         = $this->getView($e->getParam('application'));

        // Set objects into this module's Listener (see method in this class)
        $viewListener = $this->getViewListener($view, $config);
        // Inject View and Config objects into this method's container, $e 
        $e->getParam('application')->events()->attachAggregate($viewListener);
        // Grab an instance of EventManager\StaticEventManager object
        $events       = StaticEventManager::getInstance();
        // Stuff StaticEventManager and DI objects into Application\View\Listener
        $viewListener->registerStaticListeners($events, $locator);
    }

    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }

        // Instantiate [module]/src/Application/View/Listener.php
        $viewListener       = new View\Listener($view, $config->layout);
        $viewListener->setDisplayExceptionsFlag($config->display_exceptions);
        $this->viewListener = $viewListener;
        return $viewListener;
    }

    protected function getView($app)
    {
        if ($this->view) {
            return $this->view;
        }

        $di   = $app->getLocator();
        $view = $di->get('view');
        $url  = $view->plugin('url');
        $url->setRouter($app->getRouter());
        $view->plugin('doctype')->setDoctype('HTML5');
        $view->plugin('headTitle')->setSeparator(' - ')
                                  ->setAutoEscape(false)
                                  ->append(
                                            'Zend Framework 2.0 -- Building 
                                                modular projects, by Mike A'
                                            );
        $view->plugin('headLink')->appendStylesheet('/css/bootstrap.min.css');
        $view->plugin('headLink')->appendStylesheet('/css/pagestyle.css');
        $this->view = $view;
        return $view;
    }
}
