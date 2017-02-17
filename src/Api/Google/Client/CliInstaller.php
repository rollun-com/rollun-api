<?php

namespace rollun\api\Api\Google\Client;

use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Google\Client\CliInstaller" install
 */
class CliInstaller extends InstallerAbstract implements InstallerInterface
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
            /* @var $cliClient ApiGoogleClientCli */
            try {
                $cliClient = $this->container->get($cliClientName);
            } catch (\Exception $exc) {
                $this->io->writeError('Can not get ApiGoogleClientCli with name: ' . $cliClientName);
                $this->io->writeError('Exception message: ' . $exc->getMessage() . '\n');
                continue;
            }
            $cliClient->setConsoleIo($this->io);

            if ($cliClient->isAccessTokenContained($cliClient->getAccessToken())) {
                $this->io->write("Cli Client with name has credential in:");
                $this->io->write($cliClient->getCredentialFullFilename());
                $this->io->write("Delete it if you want remake credential. \n");
                continue;
            }

            $authCode = $cliClient->getAuthCode();
            try {
                $cliClient->retrieveAccessToken($authCode);
            } catch (\Exception $exc) {
                $this->io->writeError('Can not get AccessToken for Cli Client with name: ' . $cliClientName);
                $this->io->writeError('Exception message: ' . $exc->getMessage() . '\n');
                continue;
            }

            try {
                $cliClient->saveCredential();
            } catch (\Exception $exc) {
                $this->io->writeError('Can not save AccessToken for Cli Client with name: ' . $cliClientName);
                $this->io->writeError('Exception message: ' . $exc->getMessage() . '\n');
                continue;
            }
        }
        exit;
    }

    public function uninstall()
    {

    }

    public function reinstall()
    {

    }

}
