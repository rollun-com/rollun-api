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

class Web extends ClientAbstract
{
    const KEY_CREDENTIAL = 'credential';

    /** state is crypted string  */
    const KEY_STATE = 'state';

    const SECRET_PATH = 'data' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'Google';

    const SECRET_NAME = 'WebClient.json';

    const KEY_WEB_CLIENT = 'webClient';

    /** @var  SessionContainer */
    protected $sessionContainer;

    /** @var  string */
    protected $authcode;

    /** @var  string */
    protected $requestState;

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
        $this->sessionContainer->{static::KEY_CREDENTIAL} = $this->getAccessToken();
    }

    /**
     * Request authCode
     * @param $state
     * @return RedirectResponse
     */
    public function getAuthCodeRedirect($state = null)
    {
        $state = $state ?: sha1(openssl_random_pseudo_bytes(1024));
        $this->sessionContainer{static::KEY_STATE} = $state;
        $this->setState($state);
        $authUrl = $this->createAuthUrl();
        return new RedirectResponse($authUrl, 302, ['Location' => filter_var($authUrl, FILTER_SANITIZE_URL)]);
    }

    /**
     * Return user unique id.
     * An identifier for the user, unique among all Google accounts and never reused.
     * @return string|null
     */
    public function getUserId()
    {
        $token = $this->getAccessToken();
        $idToken = $token['id_token'];
        if ($this->verifyIdToken($idToken)) {
            $tokenParams = explode('.', $idToken);
            list($headb64, $bodyb64, $cryptob64) = $tokenParams;
            $payload = json_decode(base64_decode($bodyb64));
            return $payload->sub;
        }
        return null;
    }

    /**
     * @return bool
     * If credential set return true another else.
     */
    public function authByCredential()
    {
        /*if ($this->getAuthCode() || $this->isAccessTokenExpired()) {
            $credential = $this->refreshCredential();
            return true;
        }
        return false;*/
        if (is_null($this->getAccessToken()) && $this->getAuthCode()) {
            $authCode = $this->getAuthCode();
            $this->fetchAccessTokenWithAuthCode($authCode);
            $this->saveCredential();
        } elseif ($this->isAccessTokenExpired()) {
            $this->refreshAccessToken();
            $this->saveCredential();
        } else {
            return false;
        }
        return true;
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

    protected function refreshAccessToken()
    {
        // save refresh token to some variable
        $refreshTokenSaved = $this->getRefreshToken();
        // update access token and pass access token to some variable
        $credential = $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        // append refresh token
        //$credential['refresh_token'] = $refreshTokenSaved;
        return $credential;
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
        $credential = isset($this->sessionContainer->{static::KEY_CREDENTIAL}) ?
            $this->sessionContainer->{static::KEY_CREDENTIAL} :
            null;
        $this->setAccessToken($credential);
    }

    /**
     * Load config from file
     * @return bool|string
     */
    protected function setConfigFromSecretFile()
    {
        $clientSecretFullFilename = static::SECRET_PATH . DIRECTORY_SEPARATOR . static::SECRET_NAME;
        if (file_exists(realpath($clientSecretFullFilename))) {
            $this->setAuthConfig($clientSecretFullFilename);
            return $clientSecretFullFilename;
        }
        return false;
    }

    /**
     * return state string
     * @return string
     */
    public function getRequestState()
    {
        return $this->requestState ?: null;
    }

    public function getResponseState()
    {
        return $this->sessionContainer->{static::KEY_STATE};
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
            $this->requestState = $query['state'];
        }
    }
}
