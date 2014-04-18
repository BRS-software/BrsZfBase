<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Console;

use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2013-04-17
 */
class Module
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function findConsoleControllers()
    {
        $reflector = new \ReflectionClass($this->module);
        $src = dirname($reflector->getFileName()) . '/src';
        if (is_dir($src)) {
            foreach (scandir($src) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $controllersPath = sprintf('%s/%s/Controller/Console', $src, $dir);
                    break;
                }
            }
            if (is_dir($controllersPath)) {
                foreach (new \DirectoryIterator($controllersPath) as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        $controllerClass = str_replace(['.php', '/'], ['', '\\'], substr($fileinfo->getPathName(), strpos($fileinfo->getPathName(), 'src/') + 4));
                        $controllers[] = new Controller($controllerClass, $this->getModuleRoute());
                    }
                }
            }
        }
        return isset($controllers) ? $controllers : [];
    }

    public function getModuleRoute()
    {
        return isset($this->module->consoleRoute) ? $this->module->consoleRoute : false;
    }

    // http://framework.zend.com/manual/2.0/en/modules/zend.console.modules.html
    public function getConsoleUsage()
    {
        $usage = [];

        array_walk($this->findConsoleControllers($this->module), function ($controller) use (&$usage) {
            if (empty($commands = $controller->getCommands())) {
                return;
            }
            $usage[] = $controller->getDesc();
            array_walk($commands, function ($command) use (&$usage) {
                $usage[$command->getRoute()] = $command->getDesc();
                array_walk($command->getOptions(), function ($desc, $option) use (&$usage) {
                    $tmp = (array) $desc;
                    array_unshift($tmp, $option);
                    $usage[] = $tmp;
                });
            });
        });

        return $usage;
    }
}