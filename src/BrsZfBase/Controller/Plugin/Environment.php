<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Closure;
use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2014-11-13
 */
class Environment extends AbstractPlugin
{
    const PRODUCTION = 'production';
    const TESTING = 'testing';
    const DEVELOPMENT = 'development';

    public static $environments = [self::PRODUCTION, self::TESTING, self::DEVELOPMENT];

    protected $env;
    protected $config;

    public function __get($prop)
    {
        switch ($prop) {
            case 'isProduction':
                return self::PRODUCTION === $this->getEnv();
            case 'notProduction':
                return self::PRODUCTION !== $this->getEnv();
            case 'isTesting':
                return self::TESTING === $this->getEnv();
            case 'isDevelopment':
                return self::DEVELOPMENT === $this->getEnv();
        }
        throw new Exception\RuntimeException('Invalid property. Available: isProduction, notProduction, isTesting, isDevelopment');
    }

    public function __invoke(Closure $isProductionFn = null, Closure $notProductionFn = null)
    {
        if ($isProductionFn) {
            $this->isProduction($isProductionFn);
        }
        if ($notProductionFn) {
            $this->notProduction($notProductionFn);
        }
        return $this;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return;
    }

    public function getEnv()
    {
        if (null === $this->env) {
            $config = $this->config;
            if (! array_key_exists('env', $config)) {
                throw new Exception\RuntimeException('Environment not defined. Add "env" key to your config file.');
            } elseif( ! in_array($config['env'], self::$environments)) {
                throw new Exception\RuntimeException('Environment invalid value. Value must be one from values: ' . implode(', ', self::$environments));
            }
            $this->env = $config['env'];
        }
        return $this->env;
    }

    public function notProduction(Closure $fn = null)
    {
        if ($fn) {
            if (self::PRODUCTION !== $this->getEnv()) {
                $fn();
            }
            return $this;
        } else {
            return self::PRODUCTION !== $this->getEnv();
        }
    }

    public function isProduction(Closure $fn = null)
    {
        return $this->call($fn, self::PRODUCTION);
    }

    public function isTesting(Closure $fn = null)
    {
        return $this->call($fn, self::TESTING);
    }

    public function isDevelopment(Closure $fn = null)
    {
        return $this->call($fn, self::DEVELOPMENT);
    }

    protected function call(Closure $fn = null, $requiredEnv)
    {
        if ($fn) {
            if ($requiredEnv === $this->getEnv()) {
                $fn();
            }
            return $this;
        } else {
            return $requiredEnv === $this->getEnv();
        }
    }
}
