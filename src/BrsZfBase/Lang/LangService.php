<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Lang;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Brs\Zf\ServiceManager\ServiceManagerAwareTrait;
use BrsZfSloth\Assert;
use Closure;
use BrsZfBase\Exception\RuntimeException;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2015-07-28
 */
class LangService implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    protected $defaultLang;
    protected $lang;

    public static $lang2Locale = [
        'en' => 'en_GB',
        'us' => 'en_US',
    ];

    public static function convertLang2Locale($symbol)
    {
        $symbol = strtolower($symbol);
        if (isset(self::$symbol2Locale[$symbol])) {
            return self::$symbol2Locale[$symbol];
        } else {
            return strtoupper($symbol) . '_' . $symbol;
        }
    }

    public function setDefaultLang($lang)
    {
        $this->defaultLang = $lang;
        return $this;
    }

    public function getDefaultLang()
    {
        $lang = $this->getLangSymbol($this->defaultLang);
        if (empty($lang)) {
            throw new RuntimeException('Default language no set');
        }
        return $lang;
    }

    // public function getRouterLang()
    // {
    //     $routeMatch = $this->getController()->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
    //     // $routeMatch->setParam('lang',1);
    //     dbgod($routeMatch->getParam('lang'));

    // }

    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    public function getLang()
    {
        $lang = $this->getLangSymbol($this->lang);
        if (empty($lang)) {
            $lang = $this->getDefaultLang();
        }
        return $lang;
    }

    public function getLocale()
    {
        return self::convertLang2Locale($this->getLang());
    }

    protected function getLangSymbol($lang)
    {
        return trim($lang instanceof Closure ? $lang($this->getServiceManager()) : $lang);
    }
}