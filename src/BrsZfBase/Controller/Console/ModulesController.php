<?php

namespace BrsZfBase\Controller\Console;

use Zend\ModuleManager\ModuleManager;
use Zend\Console\Prompt;
use BrsZfBase\Console\Controller\AbstractActionController;
use BrsZfBase\Console\RoutesGenerator;

class ModulesController extends AbstractActionController
{
    public static function getConsoleOptions()
    {
        return [
            'desc' => 'Actions related with service of modules',
            'commands' => [
                'update console routes' => [
                    'literalCommand' => true,
                    'desc' => 'Updates console routes based on console controllers',
                    'method' => 'updateConsoleRoutes',
                    'options' => [
                    ]
                ],
            ]
        ];
    }

    protected function updateConsoleRoutes()
    {
        $modules = $this->getServiceLocator()->get('moduleManager')->getLoadedModules();
        $generator = new RoutesGenerator($modules);
        $file = $generator->saveConfig();
        $this->console->writeSuccessLine(sprintf('successfuly generated console routes into file %s', $file));
    }
}