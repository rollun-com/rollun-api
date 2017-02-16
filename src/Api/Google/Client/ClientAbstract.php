<?php

namespace rollun\api\Api\Google\Client;

use rollun\api\ApiException;

abstract class ClientAbstract extends \Google_Client
{

    public function __construct($config)
    {
        parent::__construct($config);

        $clientSecretFullFilename = $this->getFullSecretPath();
        if (!file_exists($clientSecretFullFilename)) {
            throw new ApiException(
            "There is not file $clientSecretFullFilename\n"
            . "See docs about client_secret.json\n"
            . "https://developers.google.com/gmail/api/quickstart/php\n"
            );
        }
        $this->setAuthConfigFile($clientSecretFullFilename);
    }

    const SECRET_PATH = 'resources'
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

    protected function isAccessTokenContained($accessToken)
    {
        if (isset($accessToken['access_token'])) {
            return true;
        }
        return false;
    }

    protected function checkAccessToken()
    {
        $accessToken = $this->getAccessToken();
        if (!$this->isAccessTokenContained($accessToken)) {
            throw new ApiException("There is not Access Token in Client");
        }
    }

    public function retrieveAccessToken($authCode)
    {
        $accessToken = $this->fetchAccessTokenWithAuthCode($authCode);
        if (!isset($accessToken['access_token'])) {
            throw new ApiException('Can not get Access Token. $authCode = ' . $authCode);
        }
    }

    public function refreshAccessToken()
    {
        $this->checkAccessToken();

        if ($this->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $this->getRefreshToken();
            if (is_null($refreshTokenSaved)) {
                throw new ApiException('Can not get Refresh Token');
            }
            // update access token
            $accessTokenUpdated = $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            if (!isset($accessTokenUpdated['access_token'])) {
                throw new ApiException('Can not get Refreshed Token');
            }
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            $this->setAccessToken($accessTokenUpdated);
        }
    }

}
