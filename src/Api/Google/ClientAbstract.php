<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.02.17
 * Time: 11:22
 */

namespace rollun\api\Api\Google;

use Google_Client;
use rollun\api\ApiException;

abstract class ClientAbstract extends Google_Client implements ClientInterface
{

    const SECRET_PATH = 'resources/Api/Google/';

    /** @var string $clientName */
    protected $clientName;

    /** @var  mixed */
    protected $credential;

    /**
     * load saved credential
     * @return array
     */
    abstract public function loadCredential();

    /**
     * save credential
     * @return void
     */
    abstract public function saveCredential();

    /**
     * Request authCode
     * @param $state
     */
    abstract public function requestAuthCode($state);

    /**
     * Load config from file
     * @return bool|string
     */
    protected function setConfigFromSecretFile()
    {
        $clientSecretFilename = $this->getClientName() . '.json';
        $clientSecretFullFilename = static::SECRET_PATH . $clientSecretFilename;
        if (!file_exists(realpath($clientSecretFullFilename))) {
            $this->setAuthConfig($clientSecretFullFilename);
            return $clientSecretFullFilename;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }


    /**
     * @return bool
     * If credential set return true another else.
     */
    public function authByCredential()
    {
        $credential = $this->getCredential();
        if ($credential) {
            if ($this->isAccessTokenExpired()) {
                $credential = $this->refreshCredential();
                $this->setCredential($credential);
                return true;
            }
            return true;
        }
        return false;
    }

    /**
     * refresh credential
     * @return array
     * @throws ApiException
     */
    protected function refreshCredential()
    {
        // save refresh token to some variable
        $refreshTokenSaved = $this->getRefreshToken();
        if (isset($refreshTokenSaved)) {
            // update access token
            $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // pass access token to some variable
            $credential = $this->getCredential();
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            return $credential;
        }
        throw new ApiException("RefreshToken not set!");
    }

    /**
     * @param $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        $this->saveCredential();
    }

    /**
     * @return array
     */
    protected function getCredential()
    {
        $credential = $this->getAccessToken();
        $credential  = $credential ?: $this->loadCredential();
        return $credential;
    }
}
