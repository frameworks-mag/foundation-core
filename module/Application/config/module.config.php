<?php
return array(
    'layout'                => 'layouts/layout.phtml',
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'index' => 'Application\Controller\IndexController',
                'error' => 'Application\Controller\ErrorController',
                'view'  => 'Zend\View\PhpRenderer',
            ),
            /**
             * Akrabat, 17Jan12
             * Inject the PluginBroker into the ActionController so that we pick
             * up any module's controller plugins that they have injected into 
             * it. By injecting into ActionController, all controllers that 
             * extend it will automatically pick up this injection without 
             * having to inject themselves which of course saves a lot of typing
             * for developers and stops them wondering why a given plugin isn't 
             * working in a particular controller\!
             */
            'Zend\Mvc\Controller\ActionController' => array( 
                'parameters' => array( 
                    'broker'       => 'Zend\Mvc\Controller\PluginBroker', 
                ), 
            ), 
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options'  => array(
                        'script_paths' => array(
                            'application' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'routes' => array(
        'home' => array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route'    => '/',
                'defaults' => array(
                    'controller' => 'index',
                    'action'     => 'index',
                ),
            ),
        ),
    ),
);
