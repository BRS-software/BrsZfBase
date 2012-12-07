<?php
namespace BrsZfBase;

use ZfcBase\Module\AbstractModule;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;

class Module extends AbstractModule
{
    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return 'BrsZfBase';
    }

    // public function onbootstrap() {
    //     debuge(new \Brs\Zf\Base\Module\AbstractModule);
    // }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->getDir() . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->getNamespace() => $this->getDir() . '/src/' . str_replace('\\', '/', $this->getNamespace()),
                ),
            ),
        );

    }
    // public function getServiceConfig()
    // {
    //     return array(
    //         'factories' => array(
    //             'brssloth_module_options' => function ($sm) {
    //                 $config = $sm->get('Config');
    //                 return new Options\ModuleOptions(isset($config['brssloth']) ? $config['brssloth'] : array());
    //             },
    //             // 'db-adapter' => function($sm) {
    //             //     $config = $sm->get('config');
    //             //     $config = $config['db'];
    //             //     $dbAdapter = new DbAdapter($config);
    //             //     return $dbAdapter;
    //             // },
    //         ),
    //     );
    // }
}
