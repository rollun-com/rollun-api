<?php

namespace rollun\api\Api\Gmail;

use rollun\api\Api\Gmail\GmailClient;
use Google_Service_Gmail;
use rollun\api\Api\Google\CredentialsInstallerAbstract;

class CredentialsInstaller extends CredentialsInstallerAbstract
{

    const CLIENT_CLASS = GmailClient::class; // child of  GoogleClient like 'MyClass::class'

    //const APPLICATION_NAME = 'Gmail API PHP Quickstart';

    protected $scopes = array(Google_Service_Gmail::GMAIL_READONLY);

}
