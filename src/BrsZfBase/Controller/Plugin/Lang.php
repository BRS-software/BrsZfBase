<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2015-07-27
 */
class Lang extends AbstractPlugin
{
    public function __invoke()
    {
        return $this;
    }

    public function getDefault()
    {
        return $this->getController()->getServiceLocator()->get('lang.service')->getDefaultLang();
    }

    public function getCurrent()
    {
        return $this->getController()->getServiceLocator()->get('lang.service')->getLang();
    }
}