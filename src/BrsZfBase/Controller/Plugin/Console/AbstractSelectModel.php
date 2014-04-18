<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfAppEngine\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Console\Prompt;
use BrsZfSloth\Exception\NotFoundException;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2014-04-18
 */
class SelectPage extends AbstractPlugin
{
    public function __invoke($pageId, $appId)
    {
        $sm = $this->getController()->getServiceLocator();
        $console = $sm->get('console');
        $pageRepo = $sm->get('appengine.page.repository');

        if ($pageId) {
            try {
                return $pageRepo->get('id', (int) $pageId);
            } catch (NotFoundException $e) {
                $console->writeErrorLine("Invalid page ID");
            }
        }

        $list = array_map_closure(
            $pageRepo->fetch(function($s, $c) use ($appId) {
                $s->where([
                    $c('{idApplication}') => $appId
                ]);
                $s->reset('order')->order('name');
            }),
            function ($page) {
                return sprintf('%s) %s [%s]', str_pad($page->getId(), 3, ' ', STR_PAD_LEFT), $page->getName(), $page->getLang());
            }
        );

        print "Select page from below list:\n" . implode("\n", $list) . "\n";

        while (true) {
            try {
                return $pageRepo->get('id', (new Prompt\Number("Enter page ID:"))->show());

            } catch (NotFoundException $e) {
                print "Invalid ID, try again...\n";
            }
        }
    }
}
