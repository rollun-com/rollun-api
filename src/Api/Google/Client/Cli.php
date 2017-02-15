<?php

namespace rollun\api\Api\Google\Client;

use rollun\api\ApiException;
use Composer\IO\ConsoleIO;
use rollun\api\Api\Google\Utils as ApiGoogleUtils;
use rollun\api\Api\Google\Client\Factory\ConsoleIoFactory;

//TODO: rework with new interface
class Cli extends ClientAbstract
{

    const CREDENTIAL_COMMON_PATH = 'resources'
            . DIRECTORY_SEPARATOR . 'Api'
            . DIRECTORY_SEPARATOR . 'Google'
            . DIRECTORY_SEPARATOR . 'Cli';
    const CREDENTIAL_FILENAME = 'credential.json';

    protected $consoleIo;
    protected $clientName;

    public function __construct($config, $clientName, ConsoleIO $consoleIo = null)
    {
        $this->consoleIo = $consoleIo;

        $clientEmail = $this->getClientEmail();
        if (!isset($clientEmail)) {
            throw new ApiException('login_hint (Email) is not set in config');
        }
        if (strpos($clientEmail, '@gmail.com') === false) {
            throw new ApiException('Google Api Client email must contane "@gmail.com"');
        }

        $this->clientName = $clientName; //service name

        parent::__construct($config);
        $this->setLoginHint($this->clientEmail);
        $this->addScope('https://www.googleapis.com/auth/EMAIL!!!!!!!!');
        $this->setAccessType('offline');
    }

    public static function getClientEmail()
    {
        return $this->getConfig('login_hint');
    }

    protected function getCredentialPath()
    {
        return rtrim(static::CREDENTIAL_COMMON_PATH, '\\/')
                . DIRECTORY_SEPARATOR
                . ApiGoogleUtils::convertGmailToFilename($this->getClientEmail());
    }

    protected function getCredentialFilename()
    {
        return 'credential_'
                . ApiGoogleUtils::convertStringToFilename($this->clientName)
                . '.json';
    }

    public function getCredentialFullFilename()
    {
        return rtrim($this->getCredentialPath(), '\\/')
                . DIRECTORY_SEPARATOR . $this->getCredentialFilename();
    }

    public function getConsoleIo()
    {

        if (isset($this->consoleIo)) {
            return $this->consoleIo;
        }
        $consoleIoFactory = new ConsoleIoFactory;
        $this->consoleIo = $consoleIoFactory->createConsoleIO();
        return $this->consoleIo;
    }

    /**
     * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Gmail\CredentialsInstaller" install
     */
    public function getAuthCode()
    {
        if (php_sapi_name() != 'cli') {
            throw new ApiException('This application must be run on the command line.');
        }
        $authUrl = $this->createAuthUrl();
        $consoleIo = $this->getConsoleIo();
        $consoleIo->write("Open the following link in your browser:\n$authUrl\n");
        $authCode = $consoleIo->ask('Enter verification code: ');
        return trim($authCode);
    }

    public function retrieveCredential($authCode)
    {
        $accessToken = $this->fetchAccessTokenWithAuthCode($authCode);
        if (!isset($accessToken['access_token'])) {
            throw new ApiException('Can not get Access Token. $authCode = ' . $authCode);
        }
    }

    public function saveCredential()
    {
        if (php_sapi_name() != 'cli') {
            throw new ApiException('This application must be run on the command line.');
        }
        $creditionalFullFilename = $this->getCreditionalFullFilename();
        // Store the credentials to disk.
        if (!file_exists(dirname($creditionalFullFilename))) {
            mkdir(dirname($creditionalFullFilename), 0766, true);
        }
        $accessToken = $this->getAccessToken();
        file_put_contents($creditionalFullFilename, json_encode($accessToken));
        return "Credentials saved to $creditionalFullFilename\n";
    }

    /**
     * load saved credential
     * @return array
     */
    public function loadCredential()
    {
        $creditionalFullFilename = $this->getCredentialFullFilename();
        if (file_exists($creditionalFullFilename)) {
            $accessToken = json_decode(file_get_contents($creditionalFullFilename), true);
        } else {
            throw new ApiException('Can not get Saved Token. File ' . $creditionalFullFilename . ' is absent.');
        }
        $this->setAccessToken($accessToken);
        $this->refreshAccessToken();
    }

    public function refreshAccessToken()
    {
        $accessToken = $this->getAccessToken();
        if (is_null($accessToken)) {
            throw new ApiException('Can not get Access Token');
        }
        if ($this->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $this->getRefreshToken();
            if (is_null($refreshTokenSaved)) {
                throw new ApiException('Can not get Refresh Token');
            }
            // update access token
            $accessTokenUpdated = $this->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            if (is_null($accessTokenUpdated)) {
                throw new ApiException('Can not get Refreshed Token');
            }
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            $this->setAccessToken($accessToken);
            $this->saveCredential();
        }
    }

}
