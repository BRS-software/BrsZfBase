<?php
namespace BrsZfBase\Module;

use ZfcBase\Module\AbstractModule as Module;

abstract class AbstractModule extends Module
{
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
}