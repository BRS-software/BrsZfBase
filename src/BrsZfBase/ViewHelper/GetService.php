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

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2014-11-05
 */
class GetService extends AbstractHelper implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    public function __invoke($name = null)
    {
        if ($name) {
            return $this->getService($name);
        }
        return $this->getServiceManager();
    }
}
