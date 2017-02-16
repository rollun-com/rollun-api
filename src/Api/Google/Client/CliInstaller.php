<?php

namespace rollun\api\Api\Google;

use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\GoogleClient;
use \Google_Client;
use rollun\api\Api\Google\Client\Factory\CliAbstractFactory;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;

abstract class CliInstaller extends InstallerAbstract implements InstallerInterface
{

    public function install()
    {
        if (php_sapi_name() != 'cli') {
            throw new Exception('This application must be run on the command line.');
        }
        $cliAbstractFactory = new CliAbstractFactory();
        $cliClientsNames = $cliAbstractFactory->getAllServicesNames($this->container);
        foreach ($cliClientsNames as $cliClientName) {
            /* @var $cliClient ApiGoogleClientCli */
            try {
                $cliClient = $this->container->get($cliClientName);
            } catch (\Exception $exc) {
                $this->io->writeError('Can not get ApiGoogleClientCli with name: ' . $cliClientName);
                $this->io->writeError('Exception message: ' . $exc->getMessage());
            }
            $cliClient->setConsoleIo($this->io);
        }



        $credentialsPath = $this->getCredentialsPath();
        if (file_exists($credentialsPath)) {
            $this->io->writeError("Credentials exist in $credentialsPath\nDelete it for remake and restart this script\n");
        } else {
            $clientSecretPath = $this->getClientSecretPath();
            if (!file_exists($clientSecretPath)) {
                $this->io->writeError(
                        "There is not file $clientSecretPath\nSee docs about client_secret.json\nhttps://developers.google.com/gmail/api/quickstart/php\n"
                );
                exit;
            }
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

    protected function checkCredential()
    {
        $credentialFullFilename = $cliClient->getCredentialFullFilename();
        if (!file_exists($credentialFullFilename)) {
            $this->io->write('File with ');
        }
    }

}
