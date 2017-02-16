<?php

namespace rollun\api\Api\Google;

use rollun\api\ApiException;
use rollun\api\Api\Google\Client\ClientAbstract;

class Client extends ClientAbstract
{

    public $clientName;

    public function __construct($config, $clientName = null)
    {
        parent::__construct($config);
        $this->clientName = $clientName; //service name

        $this->setAccessType('offline');
        $accessToken = $this->loadCredential();
        //$accessToken may be null
        $this->setAccessToken($accessToken);
        if ($this->isAccessTokenContained($accessToken)) {
            $this->refreshAccessToken();
        }
    }

    public function refreshAccessToken()
    {
        parent::refreshAccessToken();
        try {
            $this->saveCredential();
        } catch (\Exception $exc) {
            return new ApiException(
                    'Can not save refreshed Credential', 0, $exc
            );
        }
    }

    /**
     * Return $accessToken or null if saved Token don't exist
     * Throw exception only if i/o error rise
     * @return array|null
     */
    public function loadCredential()
    {
        return null;
    }

    /**
     * Save $accessToken from this client. Use $accessToken = $this->getAccessToken();
     *
     * @return mix
     */
    public function saveCredential()
    {
        $class = get_class($this);
        throw new ApiException("Method saveCredential of class $class is not exist");
    }

    /**
     * Metho like loadCredential(), but throw new Exception if $accessToken was not saved
     *
     * @return array
     */
    protected function getCredential()
    {
        $accessToken = $this->loadCredential();
        if (!$this->isAccessTokenContained($accessToken)) {
            throw new ApiException('There is not saved Access Token');
        }
        return $accessToken;
    }

    /**
     * @return void
     */
    protected function setCredential()
    {
        $accessToken = $this->getCredential();
        $this->setAccessToken($accessToken);
    }

}
