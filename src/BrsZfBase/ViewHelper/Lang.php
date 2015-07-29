<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Brs\Zf\ServiceManager\ServiceManagerAwareTrait;
use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2015-07-29
 */
class Lang extends AbstractHelper implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    public function __invoke()
    {
        return $this;
    }

    public function getDefault()
    {
        return $this->getService('lang.service')->getDefaultLang();
    }

    public function getCurrent()
    {
        return $this->getService('lang.service')->getLang();
    }
}
