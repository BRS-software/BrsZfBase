<?php

namespace BrsZfBase\Controller;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Confirm;
use Zend\Console\Prompt\Select;

class DeployController extends AbstractActionController
{
    // public function __construct(ModuleManager $moduleManager)
    // {
    //     dbgd($moduleManager);# code...
    // }
    public function deployAction()
    {
        // dbgod($this->getServiceLocator()->get('moduleManager')->getLoadedModules()['BrsZfSloth']->getDir());
        // dbgod($this->getEvent()->getApplication()->getServiceManager()->get('moduleManager'));
        // dbgod($this->get('moduleManager'));
        // dbgd($this->params('module'), 'deploy');
    }

    public function updbAction()
    {
        # code...
        // dbgd('updb');

    }

    protected function runLiquibase($changelogfile)
    {
        $settings = (OS == 'unix')
            ? "--driver=org.postgresql.Driver --logLevel=fine --url=\"jdbc:postgresql://%s:%s/%s\" --changeLogFile=\"%s\" --username='%s' --password='%s' --contexts=%s"
            : "--driver=org.postgresql.Driver --logLevel=fine --url=\"jdbc:postgresql://%s:%s/%s\" --changeLogFile=\"%s\" --username=\"%s\" --password=\"%s\" --contexts=%s";

        $cmd = (OS == 'unix') ? './library/liquibase/liquibase' : '.\library\liquibase\liquibase.bat';

        $params = sprintf($settings, $this->host, $this->port, $this->dbName, $changelogfile, $this->username, $this->password, $this->context);

        $cmd = sprintf('%s %s %s', $cmd, $params, 'update');

        Clix::message('Executing: %s', $cmd);
        Clix::exec($cmd);
    }

    protected function aggregateLiquid()
    {
        $outputFilename = $this->params('output');
        $modules        = $this->moduleManager->getLoadedModules();

        $writer = new XMLWriter();
        $writer->openURI($outputFilename);
        $writer->setIndent(true);
        $writer->setIndentString('    ');
        $writer->startDocument('1.0', 'utf-8');

        $writer->startElement('databaseChangeLog');
        $writer->writeAttribute('xmlns', 'http://www.liquibase.org/xml/ns/dbchangelog');
        $writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $writer->writeAttribute('xmlns:ext', 'http://www.liquibase.org/xml/ns/dbchangelog-ext');
        $writer->writeAttribute(
            'xsi:schemaLocation',
            'http://www.liquibase.org/xml/ns/dbchangelog'
            . ' http://www.liquibase.org/xml/ns/dbchangelog/dbchangelog-2.0.xsd'
            . ' http://www.liquibase.org/xml/ns/dbchangelog-ext'
            . ' http://www.liquibase.org/xml/ns/dbchangelog/dbchangelog-ext.xsd'
        );

        foreach ($modules as $module) {
            if (!method_exists($module, 'getLiquibasePath')) {
                continue;
            }

            $writer->startElement('include');
            $writer->writeAttribute('file', $module->getLiquibasePath());
            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();

        return "Aggregated change set generated.\n";
    }
}