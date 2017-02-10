<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.02.17
 * Time: 10:32
 */

namespace rollun\api\Api\Google\Client;

use rollun\api\ApiException;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use Psr\Http\Message\ServerRequestInterface as Request;

class Web extends \Google_Client
{

    const SECRET_PATH = 'data/Api/Google/';

    const SECRET_NAME = 'WebClient';

    const KEY_WEB_CLIENT = 'webClient';

    /** @var  SessionContainer */
    protected $sessionContainer;

    /** @var  string */
    protected $authcode;

    /** @var  string */
    protected $state;

    public function __construct(array $config, SessionContainer $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
        parent::__construct($config);
        $this->saveCredential();
        $this->setConfigFromSecretFile();
    }

    /**
     * save credential
     * @return void
     */
    protected function saveCredential()
    {
        $this->sessionContainer->credential = $this->getAccessToken();
    }

    /**
     * Request authCode
     * @param $state
     * @return RedirectResponse
     */
    public function requestAuthCode($state)
    {
        $this->sessionContainer->state = $state;
        $this->setState($state);
        $authUrl = $this->createAuthUrl();
        return new RedirectResponse($authUrl, 302, ['Location' => filter_var($authUrl, FILTER_SANITIZE_URL)]);
    }

    /**
     * Return user unique id.
     * An identifier for the user, unique among all Google accounts and never reused.
     * @return string|null
     */
    public function getUniqueId()
    {
        $token = $this->getAccessToken();
        $idToken = $token['id_token'];
        if ($this->verifyIdToken($idToken)) {
            $tks = explode('.', $idToken);
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $playload = json_decode(base64_decode($bodyb64));
            return $playload->sub;
        }
        return null;
    }

    /**
     * @return bool
     * If credential set return true another else.
     */
    public function authByCredential()
    {
        if ($this->getAuthCode() || $this->isAccessTokenExpired()) {
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
        } elseif (($authCode = $this->getAuthCode()) !== null) {
            $credential = $this->fetchAccessTokenWithRefreshToken($authCode);
            return $credential;
        }
        throw new ApiException("RefreshToken and AuthCode not set!");
    }

    /**
     * @return array
     */
    protected function getCredential()
    {
        $this->loadCredential();
        return $this->getAccessToken();
    }

    /**
     * load saved credential
     */
    protected function loadCredential()
    {
        $credential = isset($this->sessionContainer->credential) ? $this->sessionContainer->credential : null;
        $this->setAccessToken($credential);
    }

    /**
     * @param $credential
     */
    public function setCredential($credential)
    {
        $this->setAccessToken($credential);
        $this->saveCredential();
    }

    /**
     * Load config from file
     * @return bool|string
     */
    protected function setConfigFromSecretFile()
    {

        $clientSecretFullFilename = static::SECRET_PATH . DIRECTORY_SEPARATOR . static::SECRET_NAME;
        if (!file_exists(realpath($clientSecretFullFilename))) {
            $this->setAuthConfig($clientSecretFullFilename);
            return $clientSecretFullFilename;
        }
        return false;
    }

    /**
     * return state string
     * @return string
     */
    public function getState()
    {
        return $this->state ?: null;
    }

    /**
     * init object by request data.
     * @param Request $request
     */
    public function initByRequest(Request $request)
    {
        $query = $request->getQueryParams();
        if (isset($query['code'])) {
            $this->setAuthCode($query['code']);
        }
        if (isset($query['state'])) {
            $this->state = $query['state'];
        }
    }
}
