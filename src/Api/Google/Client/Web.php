<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.02.17
 * Time: 10:32
 */

namespace rollun\api\Api\Google;

use Zend\Diactoros\Response\RedirectResponse;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;


class Web extends ClientAbstract
{

    /** @var  SessionContainer */
    protected $sessionContainer;

    /** @var  string */
    protected $authcode;

    public function __construct(array $config)
    {
        SessionManager::class;
        parent::__construct($config);
    }

    /**
     * load saved credential
     * @return array
     */
    public function loadCredential()
    {
        return $this->sessionContainer->credential ?: null;
    }

    /**
     * save credential
     * @return void
     */
    public function saveCredential()
    {
        $this->sessionContainer->credential= $this->credential;
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
}
