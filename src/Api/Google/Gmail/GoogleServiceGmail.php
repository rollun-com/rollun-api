<?php

namespace rollun\api\Api\Google\Gmail;

use \Google_Service_Gmail;
//use zaboy\res\Di\InsideConstruct;
use rollun\api\Api\Google\Client\ClientAbstract;

class GoogleServiceGmail extends Google_Service_Gmail
{

    public function __construct(ClientAbstract $gmailGoogleClient = null)
    {
        parent::__construct($gmailGoogleClient);
    }

    /*
     *
     * public 'emailAddress' => string 'test@gmail.com' (length=25)
     * public 'historyId' => string '2845000' (length=7)
     * public 'messagesTotal' => int 84289
     * public 'threadsTotal' => int 49361
     */

    public function getProfile($email = 'me')
    {
        return $this->users->getProfile($email);
    }

}
