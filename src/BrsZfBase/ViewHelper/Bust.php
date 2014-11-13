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
 * @version 1.0 2014-11-13
 */
class Bust extends AbstractHelper implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    public function __invoke()
    {
        $env = $this->getService('ControllerPluginManager')->get('environment');
        if ($env->isDevelopment()) {
            return time();
        } else {
            $config = $this->getService('config');
            if (! array_key_exists('version', $config)) {
                throw new Exception\RuntimeException('Application version not found. Add key "version" to your application configuration.');
            }

            return substr(md5($config['version']), 0, 3);
        }
        // if ($env->isDevelopment())
    }
}
