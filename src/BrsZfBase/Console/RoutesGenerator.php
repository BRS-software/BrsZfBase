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
class RoutesGenerator
{
    protected static $routesOutputFile = 'data/console_routes.json';
    protected static $routesCache;
    protected $modules = [];

    public static function readRoutes()
    {
        if (null === self::$routesCache) {
            if (file_exists(self::$routesOutputFile)) {
                self::$routesCache = json_decode(file_get_contents(self::$routesOutputFile), true);
            } else {
                self::$routesCache = [];
            }
        }
        return self::$routesCache;
    }

    public static function readControllersInvokables()
    {
        $config = [];
        foreach (self::readRoutes() as $cfg) {
            $controller = $cfg['options']['defaults']['controller'];
            // $config[substr($controller, 0, strrpos($controller, 'Controller'))] = $controller;
            $config[$controller] = $controller;
        }
        // dbgd($config);
        return $config;
    }

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function getRoutesConfig()
    {
        $config = [];
        array_walk($this->modules, function ($module) use (&$config) {
            $module = new Module($module);
            array_walk($module->findConsoleControllers($module), function ($controller) use (&$config) {
                if (empty($commands = $controller->getCommands())) {
                    return;
                }

                // route to controller - action by user selected from list
                $routeToController = $controller->getRoute();
                $config[$routeToController]['options'] = [
                    'route' => $routeToController,
                    'defaults' => [
                        'controller' => $controller->getControllerClass(),
                        'action' => 'index',
                    ],
                ];
                array_walk($commands, function ($command) use (&$config) {
                    $route = $command->getRoute();
                    $config[$route]['options'] = [
                        'route' => $route,
                        'defaults' => [
                            'controller' => $command->getControllerClass(),
                            'action' => 'index',
                            'command' => $command->getName(),
                        ],
                    ];
                });
            });
        });
        return $config;
    }

    public function saveConfig()
    {
        file_put_contents(self::$routesOutputFile, json_encode($this->getRoutesConfig(), JSON_PRETTY_PRINT));
        return self::$routesOutputFile;
    }
}