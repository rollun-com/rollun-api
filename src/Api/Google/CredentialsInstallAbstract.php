<?php

namespace rollun\api\Api\Google;

use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\GoogleClient;
use \Google_Client;

abstract class CredentialsInstallAbstract extends InstallerAbstract implements InstallerInterface
{

    // Overide thees :
    const CLIENT_CLASS = false; // child of  GoogleClient like 'MyClass::class'
    const APPLICATION_NAME = false; //'Gmail API PHP Quickstart'

    protected $scopes = false; // array(Google_Service_Gmail::GMAIL_READONLY)
    //^^^^^^^^^^^^^^^^^^^

    /**
     *
     * @var Google_Client
     */
    protected $client;

    public function install()
    {

        $credentialsPath = $this->getCredentialsPath();
        if (file_exists($credentialsPath)) {
            $this->io->writeError("Credentials exist in $credentialsPath\nDelete it for remake and restart this script\n");
        } else {
            $clientSecretPath = $this->getClientSecretPath();
            $scopes = implode(' ', $this->scopes);
            $this->initClient(static::APPLICATION_NAME, $scopes, $clientSecretPath);
            $authUrl = $this->client->createAuthUrl();
            $this->io->write("Open the following link in your browser:\n$authUrl\n");
            $authCode = trim($this->io->ask('Enter verification code: '));
            // Exchange authorization code for an access token.
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
            $message = $this->writeCredential($accessToken);
            print($message);
        }
        exit;
    }

    public function uninstall()
    {

    }

    public function reinstall()
    {

    }

    protected function initClient($applicationName, $scopes, $clientSecretPath)
    {
        $client = new Google_Client();
        $client->setApplicationName($applicationName);
        $client->setScopes($scopes);
        $client->setAuthConfig($clientSecretPath);
        $client->setAccessType('offline');
        $this->client = $client;
    }

    protected function writeCredential($accessToken)
    {

        $credentialsPath = $this->getCredentialsPath();
        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0766, true);
        }

        file_put_contents($credentialsPath, json_encode($accessToken));
        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($this->client->getAccessToken()));
        }

        return "Credentials saved to $credentialsPath\n";
    }

    protected function getCredentialsPath()
    {
        $clientClass = static::CLIENT_CLASS;
        return call_user_func([$clientClass, 'getCredentialFullName']);
    }

    protected function getClientSecretPath()
    {
        $clientClass = static::CLIENT_CLASS;
        return $clientClass::CLIENT_SECRET_FULL_PATH;
    }

}
