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
class Controller
{
    protected $class;
    protected $moduleRoute;

    public function __construct($controllerClass, $moduleRoute)
    {
        if (! class_exists($controllerClass)) {
            throw new Exception\LogicException(
                sprintf('controller class %s not exists', $controllerClass)
            );
        }
        $this->class = $controllerClass;
        $this->moduleRoute = $moduleRoute;
    }

    public function getControllerClass()
    {
        return $this->class;
    }

    public function getConsoleOptions()
    {
        if (method_exists($this->class, 'getConsoleOptions')) {
            return forward_static_call([$this->class, 'getConsoleOptions']);
        } else {
            return [];
        }
    }

    public function getConsoleCmmandsConfig()
    {
        $opts = $this->getConsoleOptions();
        return isset($opts['commands']) ? $opts['commands'] : [];
    }

    public function getDesc()
    {
        $opts = $this->getConsoleOptions();
        return isset($opts['desc']) ? $opts['desc'] : '';
    }

    public function getCommands()
    {
        foreach ($this->getConsoleCmmandsConfig() as $command => $options) {
            $commands[] = new Command($this, $command, $options);
        }
        return isset($commands) ? $commands : [];
    }

    public function getRoute()
    {
        if ($this->moduleRoute) {
            $command[] = $this->moduleRoute;
        }
        $command[] = strtolower(str_replace('Controller', '', substr($this->class, strrpos($this->class, '\\') + 1)));
        return implode(' ', $command);
    }
}