<?php

return [
    'BrsZfBase' => [
        'options' => [
            'lang_service' => [
                'defaultLang' => function ($sm) {
                    return isset($_SERVER['MAIN_LANG']) ? $_SERVER['MAIN_LANG'] : 'en';
                },
                'lang' => function ($sm) {
                    $routeMatch = $sm->get('Application')->getMvcEvent()->getRouteMatch();
                    return $routeMatch->getParam('lang');
                },
            ],
        ],
    ],
    'console' => [
        'router' => [
            'routes' => array_merge([
                // route to generateing other routes first time
                'base-update-console-routes' => [
                    'options' => [
                        'route'    => 'update console routes',
                        'defaults' => [
                            'controller' => 'BrsZfBase\Controller\Console\ModulesController',
                            'action'     => 'index',
                            'command'    => 'update console routes',
                        ],
                    ],
                ]
            ], BrsZfBase\Console\RoutesGenerator::readRoutes())
        ],
    ],
    'service_manager' => [
        'factories' => [
            'lang.service' => function ($sm) {
                $options = $sm->get('moduleManager')->getModule('BrsZfBase')->getOption('lang_service');
                $s = new BrsZfBase\Lang\LangService;
                $s->setDefaultLang($options['defaultLang']);
                $s->setLang($options['lang']);
                return $s;
            }
        ],
    ],
    'controllers' => [
        'invokables' => array_merge([
            'BrsZfBase\Controller\Console\ModulesController' => 'BrsZfBase\Controller\Console\ModulesController',
        ], BrsZfBase\Console\RoutesGenerator::readControllersInvokables()),
    ],
    'view_helpers' => array(
        'factories' => array(
            'lang' => function($helperPluginManager) {
                $h = new \BrsZfBase\ViewHelper\Lang;
                $h->setServiceManager($helperPluginManager->getServiceLocator());
                return $h;
            },
            'getService' => function($helperPluginManager) {
                $h = new \BrsZfBase\ViewHelper\GetService;
                $h->setServiceManager($helperPluginManager->getServiceLocator());
                return $h;
            },
            'bust' => function($helperPluginManager) {
                $h = new \BrsZfBase\ViewHelper\Bust;
                $h->setServiceManager($helperPluginManager->getServiceLocator());
                return $h;
            },
            'params' => function($helperPluginManager) {
                $h = new \BrsZfBase\ViewHelper\Params;
                $h->setServiceManager($helperPluginManager->getServiceLocator());
                return $h;
                // return $helperPluginManager->getServiceLocator()->get('ControllerPluginManager')->get('params');
            },
        ),
    ),
    'controller_plugins' => [
        'invokables' => [
            'jqGrid' => 'BrsZfBase\Controller\Plugin\JqGrid',
            'getJsonPost' => 'BrsZfBase\Controller\Plugin\GetJsonPost',
            'lang' => 'BrsZfBase\Controller\Plugin\Lang',
        ],
        'factories' => [
            'environment' => function ($sm) {
                $p = new BrsZfBase\Controller\Plugin\Environment;
                $p->setConfig($sm->getServiceLocator()->get('config'));
                return $p;
            },
        ],
    ],
];