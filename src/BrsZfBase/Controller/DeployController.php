<?php

namespace BrsZfBase\Controller;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Confirm;
use Zend\Console\Prompt\Select;

class DeployController extends AbstractActionController
{
    public function __construct(ModuleManager $moduleManager)
    {
        dbgd($moduleManager);# code...
    }
    public function deployAction()
    {
        dbgod($this->getServiceLocator()->get('moduleManager')->getLoadedModules()['BrsZfSloth']->getDir());
        // dbgod($this->getEvent()->getApplication()->getServiceManager()->get('moduleManager'));
        // dbgod($this->get('moduleManager'));


    }
}