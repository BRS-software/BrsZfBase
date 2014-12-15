<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Facebook;

use Facebook\FacebookSession;
use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2014-12-11
 */
class FacebookSignedRequest
{
    protected $appId;
    protected $appSecret;
    protected $signedRequest;
    protected $parsedData;
    protected static $defaultAppId;
    protected static $defaultAppSecret;

    public function __construct($signedRequest = null, $appId = null, $appSecret = null)
    {
        $this->signedRequest = $signedRequest;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public static function setDefaultApplication($appId, $appSecret)
    {
        self::$defaultAppId = $appId;
        self::$defaultAppSecret = $appSecret;
    }

    public function parse()
    {
        if (null === $this->parsedData) {
            list($encoded_sig, $payload) = explode('.', $this->signedRequest, 2);

            // decode the data
            $sig = $this->base64_url_decode($encoded_sig);
            $this->parsedData = json_decode($this->base64_url_decode($payload), true);

            if (null === $this->parsedData) {
                throw new Exception\RuntimeException('invalid signed request');
            }

            // confirm the signature
            $expected_sig = hash_hmac('sha256', $payload, $this->appSecret ?: self::$defaultAppSecret, $raw = true);
            if ($sig !== $expected_sig) {
                error_log('Bad Signed JSON signature!');
                return null;
            }
        }
        return $this->parsedData;
    }

    public function getSession()
    {
        return new FacebookSession($this->parse()['oauth_token']);
    }

    protected function base64_url_decode($input)
    {
      return base64_decode(strtr($input, '-_', '+/'));
    }
}