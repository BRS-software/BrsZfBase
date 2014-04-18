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
    // 'view_manager' => [
    //     'template_path_stack' => [
    //         'zfcuser' => __DIR__ . '/../view',
    //     ],
    // ],
    // 'controllers' => [
    //     'invokables' => [
    //         'zfcuser' => 'ZfcUser\Controller\UserController',
    //     ],
    // ],
    'controller_plugins' => [
        'invokables' => [
            'jqGrid' => 'BrsZfBase\Controller\Plugin\JqGrid',
        ],
    ],
    'service_manager' => [
        // 'aliases' => [
        // ],
        // 'factories' => [
        // ]
    ],
];