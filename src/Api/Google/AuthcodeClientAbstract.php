<?php

namespace rollun\api\Api\Google;

use \Google_Client;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Filter\Word\SeparatorToDash;
use rollun\api\ApiException;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Gmail\CredentialsInstaller" install
 */
abstract class AuthcodeClientAbstract extends Google_Client implements AuthcodeClientInterface
{
    const SECRET_PATH = 'resources/Api/Google/';

    /**
     * @var string $clientName
     */
    protected $clientName;

    /**
     * @var string
     */
    protected $code;

    /** @var  mixed */
    protected $credential;

    /**
     * @param $state string crypt token
     */
    abstract public function codeRequest($state);

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
     * AuthcodeClientAbstract constructor.
     * @param string $clientName
     * @param array $config
     */
    public function __construct($clientName, $config = [])
    {
        $this->clientName = $clientName;
        parent::__construct($config);
        $this->code = isset($config['code']) ? $config['code'] : null;
        $this->setConfigFromSecretFile();
    }

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
     * Set authCode
     * @param $code
     */
    public function setAuthCode($code)
    {
        $this->code = $code;
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
        } elseif (($authCode = $this->getAuthCode()) !== null) {
            $credential = $this->refreshCredential();
            $this->setCredential($credential);
            return true;
        }
        return false;
    }

    /**
     * refresh credential
     * @return array
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
        } else {
            $authCode = $this->getAuthCode();
            $credential = $this->fetchAccessTokenWithRefreshToken($authCode);
        }
        return $credential;
    }

    /**
     * @return string|null
     */
    public function getAuthCode()
    {
        return $this->code;
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
