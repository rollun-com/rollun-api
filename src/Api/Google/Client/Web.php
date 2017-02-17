<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.02.17
 * Time: 10:32
 */

namespace rollun\api\Api\Google\Client;

use rollun\api\Api\Google\Client;
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

    const SECRET_PATH = 'data'
    . DIRECTORY_SEPARATOR . 'Api'
    . DIRECTORY_SEPARATOR . 'Google';

    const SECRET_FILENAME = 'client_secret.json';

    const KEY_WEB_CLIENT = 'webClient';

    /** @var  SessionContainer */
    protected $sessionContainer;

    public function __construct(array $config, SessionContainer $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
        parent::__construct($config);
        try {
            $accessToken = $this->loadCredential();
            if ($this->isAccessTokenContained($accessToken)) {
                $this->setAccessToken($accessToken);
                $this->refreshAccessToken();
            }
        } catch (ApiException $apiException) {}
    }

    /**
     * load saved credential
     */
    public function loadCredential()
    {
        if (isset($this->sessionContainer->{static::KEY_CREDENTIAL})) {
            $this->setAccessToken($this->sessionContainer->{static::KEY_CREDENTIAL});
        } else {
            throw new ApiException("Credential not saved.");
        }
    }

    public function refreshAccessToken()
    {
        if (!is_null($this->getAccessToken()) && $this->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $this->getRefreshToken();
            // update access token and pass access token to some variable
            $credential = $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // append refresh token
            if (!$credential || isset($credential['error'])) {
                return false;
            }
            $credential['refresh_token'] = $refreshTokenSaved;
            $this->setAccessToken($credential);
            $this->saveCredential();
            return true;
        }
        return false;
    }

    /**
     * save credential
     * @return void
     */
    public function saveCredential()
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

    public function authByCode($authCode)
    {
        if (isset($authCode)) {
            $credential = $this->fetchAccessTokenWithAuthCode($authCode);
            if (!$credential || isset($creds['error'])) {
                return false;
            }
            $this->saveCredential();
            return true;
        }
        return false;
    }

    public function getResponseState()
    {
        return $this->sessionContainer->{static::KEY_STATE};
    }
}
