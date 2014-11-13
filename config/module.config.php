<?php

return [
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
    'controllers' => [
        'invokables' => array_merge([
            'BrsZfBase\Controller\Console\ModulesController' => 'BrsZfBase\Controller\Console\ModulesController',
        ], BrsZfBase\Console\RoutesGenerator::readControllersInvokables()),
    ],
    'view_helpers' => array(
        'factories' => array(
            'getService' => function($sm) {
                $h = new \BrsZfBase\ViewHelper\GetService;
                $h->setServiceManager($sm->getServiceLocator());
                return $h;
            },
            'bust' => function($sm) {
                $h = new \BrsZfBase\ViewHelper\Bust;
                $h->setServiceManager($sm->getServiceLocator());
                return $h;
            },
        ),
    ),
    'controller_plugins' => [
        'invokables' => [
            'jqGrid' => 'BrsZfBase\Controller\Plugin\JqGrid',
            'environment' => 'BrsZfBase\Controller\Plugin\Environment',
        ],
    ],
];