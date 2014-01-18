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
        $config = $this->getServiceLocator()->get('config')['db'];
        preg_match('/^(\w+):(.*)/', $config['dsn'], $m);
        $config['driver'] = $m[1];
        foreach (explode(';', $m[2]) as $v) {
            $tmp = explode('=', $v);
            $config[$tmp[0]] = $tmp[1];
        }
        $config['port'] = 5432;
        $config['logLevel'] = 'severe'; // Execution log level (debug, info, warning, severe, off)

        if (PHP_OS === 'Windows') {
            $settings = '--driver=org.postgresql.Driver --logLevel=%s --url="jdbc:postgresql://%s:%s/%s" --changeLogFile="%s" --username="%s" --password="%s" --contexts=%s';
            $cmd = '.\vendor\alcaeus\liquibase\liquibase.bat';
        } else {
            $settings = "--driver=org.postgresql.Driver --logLevel=%s --url=\"jdbc:postgresql://%s:%s/%s\" --changeLogFile=\"%s\" --username='%s' --password='%s' --contexts=%s";
            $cmd = './vendor/alcaeus/liquibase/liquibase';
        }

        $changelogfile = $this->aggregateLiquid('data/liquibase/liquibase-all-modules.xml');
        $params = sprintf($settings, $config['logLevel'], $config['host'], $config['port'], $config['dbname'], $changelogfile, $config['username'], $config['password'], 'webapp');

        // putenv(sprintf('LIQUIBASE_HOME=%s', getcwd()));
        $cmd = sprintf('%s %s %s', $cmd, $params, 'update');
        printf("Executing: %s\n", $cmd);
        passthru($cmd);
    }

    protected function aggregateLiquid($outputFilename)
    {
        $dir = dirname($outputFilename);
        if (! is_dir($dir)) {
            mkdir_fix($dir, 0777);
        }
        // $outputFilename = $this->params('output');
        $modules = $this->getServiceLocator()->get('moduleManager')->getLoadedModules();

        $writer = new \XMLWriter();
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
        return $outputFilename;
    }
}