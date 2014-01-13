<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                // 'BrsZfBase_' => array(
                //     'options' => array(
                //         'route'    => 'sloth definition init [--skip-existing|-s]',
                //         'defaults' => array(
                //             'controller' => 'BrsZfSloth\Controller\Generator',
                //             'action'     => 'initdb',
                //         ),
                //     ),
                // ),
                'BrsZfBase.Deploy' => array(
                    'options' => array(
                        'route'    => 'deploy [<action>] [--module=]',
                        // 'constraints' => array(
                        //     'table' => '/^[a-z0-9_]{1,}$/'
                        // ),
                        'defaults' => array(
                            'controller' => 'BrsZfBase\Controller\Deploy',
                            'action'     => 'deploy',
                        ),
                    ),
                ),
                // 'exception' => array(
                //     'options' => array(
                //         'route'    => 'exception',
                //         'defaults' => array(
                //             'controller' => 'Application\Controller\Index',
                //             'action'     => 'exception',
                //         ),
                //     ),
                // ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'BrsZfBase\Controller\Deploy' => 'BrsZfBase\Controller\DeployController',
        ),
    ),
    // 'view_manager' => array(
    //     'template_path_stack' => array(
    //         'zfcuser' => __DIR__ . '/../view',
    //     ),
    // ),
    // 'controllers' => array(
    //     'invokables' => array(
    //         'zfcuser' => 'ZfcUser\Controller\UserController',
    //     ),
    // ),
    'controller_plugins' => array(
        'invokables' => array(
            'jqGrid' => 'BrsZfBase\Controller\Plugin\JqGrid',
        ),
    ),
    // 'service_manager' => array(
    //     'aliases' => array(
    //         'zfcuser_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
    //     ),
    // )
);
