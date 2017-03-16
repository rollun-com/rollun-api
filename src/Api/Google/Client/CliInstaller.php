<?php

namespace rollun\api\Api\Google\Client;

use Exception;
use rollun\api\Api\Google\Gmail\GmailClientInstaller;
use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Google\Client\CliInstaller" install
 */
class CliInstaller extends InstallerAbstract
{

    public function install()
    {
        if (php_sapi_name() != 'cli') {
            throw new Exception('This application must be run on the command line.');
        }
        $cliAbstractFactory = new ApiGoogleClientAbstractFactory();
        $cliClientsNames = $cliAbstractFactory->getAllClasses($this->container);
        foreach ($cliClientsNames as $cliClientName => $cliClientClass) {
            if (!is_a($cliClientClass, ApiGoogleClientCli::class, true)) {
                continue;
            }
            $try = 0;
            $cliClient= null;
            do {
                try {
                    /* @var $cliClient ApiGoogleClientCli */
                    $try++;
                    $cliClient = $this->container->get($cliClientName);
                } catch (\Exception $exc) {
                    $this->consoleIO->writeError('Can not get ApiGoogleClientCli with name: ' . $cliClientName);
                    $this->consoleIO->writeError('Exception message: ' . $exc->getMessage() . '\n');
                    $this->consoleIO->writeError('Check if google config file if exist.');
                    $this->consoleIO->askConfirmation("Config file exist, try again: \n");
                }
            } while ($try < 2 || isset($cliClient));

            if(is_null($cliClient) || !isset($cliClient)) {
                $this->consoleIO->writeError('Can not get ApiGoogleClientCli with name: ' . $cliClientName);
                continue;
            }

            $cliClient->setConsoleIo($this->consoleIO);

            if ($cliClient->isAccessTokenContained($cliClient->getAccessToken())) {
                $this->consoleIO->write("Cli Client with name has credential in:");
                $this->consoleIO->write($cliClient->getCredentialFullFilename());
                $this->consoleIO->write("Delete it if you want remake credential. \n");
                continue;
            }

            $authCode = $cliClient->getAuthCode();
            try {
                $cliClient->retrieveAccessToken($authCode);
            } catch (\Exception $exc) {
                $this->consoleIO->writeError('Can not get and save AccessToken for Cli Client with name: ' . $cliClientName);
                $this->consoleIO->writeError('Exception message: ' . $exc->getMessage() . '\n');
                continue;
            }
            $this->consoleIO->writeError('AccessToken was saved for Cli Client with name: ' . $cliClientName);
        }
        return [

        ];
    }

    public function isInstall()
    {
        $cliAbstractFactory = new ApiGoogleClientAbstractFactory();
        $cliClientsNames = $cliAbstractFactory->getAllClasses($this->container);
        foreach ($cliClientsNames as $cliClientName => $cliClientClass) {
            if (!is_a($cliClientClass, ApiGoogleClientCli::class, true)) {
                continue;
            }
            /* @var $cliClient ApiGoogleClientCli */
            try {
                $cliClient = $this->container->get($cliClientName);
            } catch (\Exception $exc) {
                return false;
            }
            $cliClient->setConsoleIo($this->consoleIO);
            return $cliClient->isAccessTokenContained($cliClient->getAccessToken());
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
                $description = "Инициализирует cli google client.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    public function getDependencyInstallers()
    {
        return [
            GmailClientInstaller::class
        ];
    }
}
