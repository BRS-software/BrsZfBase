<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase;

use BrsZfBase\Console\Module as ConsoleModule;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;
use ZfcBase\Module\AbstractModule;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2012-12-07
 */
class Module extends AbstractModule implements ConsoleUsageProviderInterface
{
    public function getConsoleUsage(Console $console)
    {
        return (new ConsoleModule($this))->getConsoleUsage();
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return 'BrsZfBase';
    }

    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $sm = $app->getServiceManager();

        // replace default zf console factory
        $allowOverride = $sm->getAllowOverride();
        $sm->setAllowOverride(true);
        $sm->setFactory('console', new \Brs\Zf\Mvc\Service\ConsoleAdapterFactory('\Brs\Zf\Console\Adapter\DevNull', '\Brs\Zf\Console\Adapter\Posix'));
        $sm->setAllowOverride($allowOverride);
    }

    public function getAutoloaderConfig()
    {
        return array(
        );

    }
}
