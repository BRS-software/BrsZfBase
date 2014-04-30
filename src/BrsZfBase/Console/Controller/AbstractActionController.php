<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Console\Controller;

use Zend\Mvc\Controller\AbstractActionController as ZfAbstractActionController;
use Zend\Console\Prompt;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\ColorInterface as Color;
use BrsZfBase\Exception;
use Brs\Stdlib\Console\CmdExec;


/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2013-04-16
 * @see http://framework.zend.com/manual/2.0/en/modules/zend.console.modules.html
 */
abstract class AbstractActionController extends ZfAbstractActionController
{
    abstract static public function getConsoleOptions();

    public function indexAction()
    {
        $this->console = $this->getServiceLocator()->get('console');
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new Exception\RuntimeException('You can only use this action from a console!');
        }

        $opts = static::getConsoleOptions()['commands'];

        if ($this->params('help')) {
            return 'x';
            print $this->getHelp();
        } else {
            $commandsList = [];
            $commands = [];

            $longestAction = max(array_map('strlen', array_keys($opts)));
            $opt = 'a';

            foreach ($opts as $cmd => $data) {
                $commands[$opt] = $cmd;
                $commandsList[$opt] = sprintf('%s  %s', str_pad($cmd, $longestAction, '   ', STR_PAD_RIGHT), isset($data['desc']) ? $data['desc'] : '');
                $opt = chr(ord($opt) + 1);
            }

            if (! $command = $this->params('command')) {
                $command = $commands[(new Prompt\Select('Select an action to run', $commandsList))->show()];
            }
            $commandData = $opts[$command];

            // replace command-name to commandName
            $command = preg_replace_callback('/(-|\s[a-z])/', function($letters) {
                $letter = substr(array_shift($letters), 1, 1);
                return ucfirst($letter);
            }, $command);

            $method = isset($commandData['method']) ? $commandData['method'] : $command;
            $args = isset($commandData['args']) ? $commandData['args'] : [];

            if (!method_exists($this, $method)) {
                $this->console->writeErrorLine(sprintf("method %s not found in %s", $method, get_class($this)));
                return;
            }
            return call_user_func_array([$this, $method], $args);
        }
    }

    protected function exec($cmd)
    {
        $c = new CmdExec;
        call_user_func_array([$c, 'setCmd'], func_get_args());
        return $c->execute();
    }
}