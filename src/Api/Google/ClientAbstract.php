<?php

namespace rollun\api\Api\Google;

use \Google_Client;
use Zend\Filter\Word\SeparatorToDash;
use rollun\api\ApiException;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Gmail\CredentialsInstaller" install
 */
abstract class ClientAbstract extends Google_Client
{

    const SECRET_PATH = 'resources/Api/Google/';

    protected $clientName;

    public function __construct($config, $clientName = null)
    {
        parent::__construct($config);
        $this->clientName = $clientName;
        $this->setConfigFromSecretFile();

        $this->setCredential();
    }

    abstract public function getAuthCode();

    abstract public function saveCredential($accessToken);

    abstract public function getSavedCredential();

    public function setConfigFromSecretFile($clientName = null)
    {
        $defaultClientName = 'client_secret';
        $clientName = $clientName? : $this->getClientName();
        $clientName = $clientName? : $this->$defaultClientName;
        $clientSecretFilename = $clientName . '.json';
        $clientSecretFullFilename = static::CLIENT_SECRET_PATH . $clientSecretFilename;
        if (!file_exists($clientSecretFullFilename)) {
            $this->setAuthConfig($clientSecretFullFilename);
            return $clientSecretFullFilename;
        }
        return false;
    }

    public function setCredential()
    {
        $accessToken = $this->getAccessToken();
        $accessToken = $accessToken? : $this->getSavedCredential();
        if ($accessToken) {
            if ($this->isAccessTokenExpired()) {
                $accessToken = $this->refreshAccessToken($accessToken);
                $this->saveCredential($accessToken);
            }
        } else {
            $authCode = $this->getAuthCode();
            $accessToken = $this->fetchAccessTokenWithAuthCode($authCode);
            $this->saveCredential($accessToken);
        }
    }

    public function refreshAccessToken($accessToken)
    {
        // save refresh token to some variable
        $refreshTokenSaved = $this->getRefreshToken();
        // update access token
        $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        // pass access token to some variable
        $accessTokenUpdated = $this->getAccessToken();
        // append refresh token
        $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

        return $accessTokenUpdated;
    }

    public static function getClientName()
    {
        return $this->clientName;
    }

    public static function convertGmailToFilename($gmailAddress)
    {
        $str = str_replace('@gmail.com', '_at_gmail_dat_com', $gmailAddress);
        $str = str_replace('.', '', $str); //a.b@gmail.com and ab@gmail.com is same
        return static::convertStringToFilename($str);
    }

    public static function convertStringToFilename($str)
    {
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
