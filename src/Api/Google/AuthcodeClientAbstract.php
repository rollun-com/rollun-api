<?php

namespace rollun\api\Api\Google;

use \Google_Client;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Filter\Word\SeparatorToDash;
use rollun\api\ApiException;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Gmail\CredentialsInstaller" install
 */
abstract class AuthcodeClientAbstract extends ConfiguredClientAbstract implements AuthcodeClientInterface
{
    const SECRET_PATH = 'resources/Api/Google/';

    /**
     * @var string
     */
    protected $authcode;

    public function __construct(array $config, $clientName)
    {
        parent::__construct($clientName, $config);
        $this->authcode = isset($config['code']) ? $config['code'] : null;
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

        if (parent::authByCredential()) {
            return true;
        } elseif (($authCode = $this->getAuthCode()) !== null) {
            $credential = $this->refreshCredential();
            $this->setCredential($credential);
            return true;
        }
        return false;
    }

    /**
     * @return string|null
     */
    public function getAuthCode()
    {
        return $this->authcode;
    }

    /**
     * Set authCode
     * @param $code
     */
    public function setAuthCode($code)
    {
        $this->authcode = $code;
    }

    /**
     * refresh credential
     * @return array
     */
    protected function refreshCredential()
    {
        try {
            $credential = parent::refreshCredential();
        } catch (ApiException $apiException) {
            $authCode = $this->getAuthCode();
            $credential = $this->fetchAccessTokenWithRefreshToken($authCode);
        }
        return $credential;
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
        $credential = $credential ?: $this->loadCredential();
        return $credential;
    }
}
