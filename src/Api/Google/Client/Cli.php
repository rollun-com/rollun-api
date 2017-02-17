<?php

namespace rollun\api\Api\Google\Client;

use rollun\api\ApiException;
use Composer\IO\ConsoleIO;
use rollun\api\Api\Google\Utils as ApiGoogleUtils;
use rollun\api\Api\Google\Client\Factory\ConsoleIoFactory;

class Cli extends ClientAbstract
{

    const CREDENTIAL_COMMON_PATH = 'data'
            . DIRECTORY_SEPARATOR . 'Api'
            . DIRECTORY_SEPARATOR . 'Google'
            . DIRECTORY_SEPARATOR . 'Cli';

    protected $consoleIo;
    public $clientName;

    public function __construct($config, $clientName, ConsoleIO $consoleIo = null)
    {
        parent::__construct($config);

        $this->consoleIo = $consoleIo;

        $clientEmail = $this->getClientEmail();
        if (!isset($clientEmail)) {
            throw new ApiException('login_hint (Email) is not set in config');
        }
        if (strpos($clientEmail, '@gmail.com') === false) {
            throw new ApiException('Google Api Client email must contane "@gmail.com"');
        }
        $this->setLoginHint($clientEmail);


        $this->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);
        $this->setAccessType('offline');
    }

    public static function getClientEmail()
    {
        return $this->getConfig('login_hint');
    }

    public static function retrieveClientEmail()
    {
        $service = new \Google_Service_Oauth2($this);
        $user = $service->userinfo->get();
        return $user->email;
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
        if (!isset($this->consoleIo)) {
            $this->setConsoleIo();
        }
        return $this->consoleIo;
    }

    public function setConsoleIo(ConsoleIO $consoleIo = null)
    {
        if (isset($consoleIo)) {
            $this->consoleIo = $consoleIo;
        }
        $consoleIoFactory = new ConsoleIoFactory;
        $this->consoleIo = $consoleIoFactory->createConsoleIO();
        $this->consoleIo = $consoleIo;
    }

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
            return null;
        }
    }

}
