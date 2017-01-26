<?php

namespace rollun\api\Api\Google;

use \Google_Client;
use Zend\Filter\Word\SeparatorToDash;

class Client extends Google_Client
{

    const CLIENT_SECRET_PATH = 'resources/Api/Google/';
    const CREDENTIALS_PATH = 'data/Api/Google/';

    public function __construct($secretKeyName, $creditionalName = null)
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
            file_put_contents($credentialFullName, json_encode($accessTokenUpdated));
        }
    }

    public static function convertNameToFilename($gmailAddress)
    {
        $str = strip_tags($gmailAddress);
        $str = str_replace('@gmail.com', '_at_gmail_dat_com', $str);
        $str = str_replace('.', '', $str); //a.b@gmail.com and ab@gmail.com is same
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

}
