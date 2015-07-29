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
class Params extends AbstractHelper implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    public function __invoke()
    {
        return call_user_func_array([$this->getService('ControllerPluginManager')->get('params'), '__invoke'], func_get_args());
    }
}
