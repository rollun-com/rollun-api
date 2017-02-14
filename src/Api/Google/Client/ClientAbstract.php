<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.02.17
 * Time: 14:38
 */

namespace rollun\api\Api\Google\Client;

abstract class ClientAbstract extends \Google_Client
{

    const SECRET_PATH = 'data'
            . DIRECTORY_SEPARATOR . 'Api'
            . DIRECTORY_SEPARATOR . 'Google';
    const SECRET_FILENAME = 'client_secret.json';

    protected function getFullSecretPath()
    {
        return rtrim($this->getSecretPath(), '\\/')
                . DIRECTORY_SEPARATOR
                . $this->getSecretFilename();
    }

    protected function getSecretPath()
    {
        return self::SECRET_PATH
                . DIRECTORY_SEPARATOR
                . end(explode('\\', get_class($this)));
    }

    protected function getSecretFilename()
    {
        return static::SECRET_FILENAME;
    }

    /**
     * @return array
     */
    protected function getCredential()
    {
        $accessToken = $this->getAccessToken();
        if (!isset($accessToken['access_token'])) {
            $this->loadCredential();
            $accessToken = $this->getAccessToken();
        }
        return $accessToken;
    }

    abstract public function loadCredential();
}
