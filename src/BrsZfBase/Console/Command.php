<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Console;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2013-04-17
 */
class Command
{
    protected $controller;
    protected $name;
    protected $config = [];

    public function __construct(Controller $controller, $name, array $config = [])
    {
        $this->controller = $controller;
        $this->name = strtolower($name);
        $this->config = $config;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return isset($this->config['options']) ? $this->config['options'] : [];
    }

    public function getControllerClass()
    {
        return $this->controller->getControllerClass();
    }

    public function getRoute()
    {
        if (! (isset($this->config['literalCommand']) && $this->config['literalCommand'])) {
            $route[] = $this->controller->getRoute();
        }
        $route[] = $this->getName();

        foreach ($this->getOptions() as $opt => $desc) {
            $route[] = $opt;
        }

        return implode(' ', $route);
    }

    public function getDesc()
    {
        return isset($this->config['desc']) ? $this->config['desc'] : '';
    }
}