<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2015-05-17
 */
class GetJsonPost extends AbstractPlugin
{
    protected $data;
    protected $isContentTypeJson;

    public function __invoke(/*$param = null, $default = null*/)
    {
        if (func_num_args() === 0) {
            return $this;
        }
        return $this->get(
            func_get_arg(0),
            func_num_args() === 2 ? func_get_arg(1) : null
        );
    }

    public function get($param = null, $default = null)
    {
        if (! $this->isContentTypeJson()) {
            throw new Exception\RuntimeException('Invalid HTTP header. Expected: Content-Type: application/json');
        }
        $data = $this->getData();
        if ($param === null) {
            return $data;
        } elseif (isset($data[$param])) {
            return $data[$param];
        } else {
            return $default;
        }
    }

    public function getData()
    {
        if (null === $this->data) {
            $input = file_get_contents('php://input');
            $this->data = json_decode($input, true);
            if ($input && null === $this->data) {
                throw new Exception\RuntimeException(
                    sprintf('Invalid JSON in POST request: %s', $input)
                );
            }
        }
        return $this->data;
    }

    public function isContentTypeJson()
    {
        if (null === $this->isContentTypeJson) {
            $ctHeader = $this->getController()->getRequest()->getHeaders('Content-Type');
            $this->isContentTypeJson = $ctHeader && $ctHeader->getFieldValue() === 'application/json';
        }
        return $this->isContentTypeJson;
    }
}