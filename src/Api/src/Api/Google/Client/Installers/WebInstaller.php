<?php

namespace rollun\api\Api\Google\Client\Installers;

use Exception;
use rollun\api\Api\Google\Client\GoogleClientInstaller;
use rollun\api\Api\Google\Client\Web;
use rollun\api\Api\Google\Gmail\GmailClientInstaller;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Google\Client\CliInstaller" install
 */
class WebInstaller extends InstallerAbstract
{

    public function install()
    {
        $this->consoleIO->write("In order to work with the Web client, 
        you need to download the file with access to your application from google developer console,
        and put it in the `data/Api/Google/Web` directory with name `client_secret.json`.");
        $this->consoleIO->askConfirmation("When a file is created - select `yes` as an answer.");

        $scopes = [
            'openid',
            \Google_Service_Gmail::GMAIL_READONLY,
            \Google_Service_Plus::PLUS_ME,
            \Google_Service_Plus::USERINFO_EMAIL,
            \Google_Service_Plus::USERINFO_PROFILE,
            \Google_Service_Plus::PLUS_LOGIN,
        ];
        $scopesKey = $this->consoleIO->select("Select scope which will be used.", $scopes, "0", false,'Value "%s" is invalid',true);

        $selectedScopes = [];
        foreach ($scopesKey as $key) {
            $selectedScopes[] = $scopes[$key];
        }

        $config = [
            ApiGoogleClientAbstractFactory::KEY_GOOGLE_API_CLIENTS => [
                Web::class => [
                    AbstractFactoryAbstract::KEY_CLASS => Web::class, //optionaly
                    ApiGoogleClientAbstractFactory::KEY_SCOPES => $selectedScopes
                ]
            ]
        ];
        return $config;
    }

    public function isInstall()
    {
        $clientAbstractFactory = new ApiGoogleClientAbstractFactory();
        if($clientAbstractFactory->canCreate($this->container, Web::class)) {
            //TODO: make more check
            return true;
        }
        return false;
    }

    public function uninstall()
    {

    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Инициализирует web google client.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    public function getDependencyInstallers()
    {
        return [
            GoogleClientInstaller::class,
        ];
    }
}
