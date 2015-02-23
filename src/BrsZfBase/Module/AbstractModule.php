<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Module;

use ZfcBase\Module\AbstractModule as Module;
use Zend\ModuleManager\ModuleManager;
use Zend\Cache\StorageFactory;
use Zend\EventManager\EventInterface as Event;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2012-12-06
 */
abstract class AbstractModule extends Module
{
    protected $serviceManager;

    public function init(ModuleManager $moduleManager)
    {
        // Remember to keep the init() method as lightweight as possible
        $events = $moduleManager->getEventManager();
        $events->attach('loadModules.post', function (Event $e) {
            $this->serviceManager = $e->getParam('ServiceManager'); // short way to service manager
            $this->setMergedConfig($this->serviceManager->get('config'));
            $this->modulesLoaded($e);
        });

        $sharedManager = $moduleManager->getEventManager()->getSharedManager();
        $sharedManager->attach('Zend\Mvc\Application', 'bootstrap', function($e) use ($moduleManager) {
            $app = $e->getParam('application');
            // $this->serviceManager = $app->getServiceManager();
            $this->bootstrap($moduleManager, $app);
        });
    }

    public function modulesLoaded(Event $e)
    {
    }

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

    /**
     * @param array $options Importatnt options keys: ttl, namespace
     */
    public function createCache(array $options)
    {
        $cache = StorageFactory::factory([
            'options' => $options,
            'adapter' => [
                'name'     =>'memcached',
                // 'ttl' => 3,
                'options'  => [
                    'servers'   => [
                        [
                            '127.0.0.1', 11211
                        ],
                    ],
                    // 'namespace'  => strtoupper($ns),
                    'liboptions' => [
                        'COMPRESSION' => true,
                        'binary_protocol' => true,
                        'no_block' => true,
                        'connect_timeout' => 100
                    ]
                ]
            ],
            'plugins' => [
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ]);
        // dbgod($cache->getOptions());
        return $cache;
    }
}