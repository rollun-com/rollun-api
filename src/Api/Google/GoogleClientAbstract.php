<?php

namespace rollun\api\Api\Google;

use \Google_Client;
use Zend\Filter\Word\SeparatorToDash;

abstract class GoogleClientAbstract extends Google_Client
{

    const CLIENT_SECRET_FULL_PATH = 'resources/Api/Google/client_secret.json';
    const CREDENTIALS_PATH = 'data/Api/Google/';

    public function __construct($config = array())
    {
        parent::__construct($config);

        if (!file_exists(static::CLIENT_SECRET_FULL_PATH)) {
            throw new \RuntimeException(static::CLIENT_SECRET_FULL_PATH . ' do not exist');
        }
        $this->setAuthConfig(static::CLIENT_SECRET_FULL_PATH);

        $this->setCredential();
        $this->checkCredentialExpired();
    }

    public static function getName()
    {
        $filter = new SeparatorToDash('\\');
        return $filter->filter(static::class);
    }

    public static function getCredentialFullName()
    {
        return static::CREDENTIALS_PATH . 'access-token' . static::getName() . '.json';
    }

    public static function getClientSecretFullPath()
    {
        return static::CLIENT_SECRET_FULL_PATH;
    }

    public function setCredential()
    {
        $credentialFullName = static::getCredentialFullName();
        if (!file_exists($credentialFullName)) {
            throw new \RuntimeException($credentialFullName . ' do not exist.');
        }
        $accessToken = json_decode(file_get_contents($credentialFullName), true);
        $this->setAccessToken($accessToken);
    }

    public function checkCredentialExpired()
    {
        if ($this->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $this->getRefreshToken();
            // update access token
            $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // pass access token to some variable
            $accessTokenUpdated = $this->getAccessToken();
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            // save to file
            $credentialFullName = static::getCredentialFullName();
            file_put_contents($credentialFullName, json_encode($this->getAccessToken()));
        }
    }

}
