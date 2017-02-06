<?php

namespace rollun\api\Api\Google;

use rollun\api\Api\Google\AuthcodeClientAbstract;
use Zend\Filter\Word\SeparatorToDash;
use rollun\api\ApiException;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

//TODO: rework with new interface
class Cli extends AuthcodeClientAbstract
{

    const SECRET_PATH = 'data/Api/Google/';

    protected $io;

    public function __construct($config, $clientName = null, $io = null)
    {
        $this->io = $io;
        parent::__construct($config, $clientName);
    }

    public function getCredentialFullFilename()
    {
        $defaultCredentialName = $this->convertStringToFilename(static::class);
        $credentialName = $this->clientName ? : $defaultCredentialName;
        $credentialName = $this->convertStringToFilename($credentialName);
        return static::SECRET_PATH . $credentialName . '.json';
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
        $composerIo = $this->getComposerIo();
        $composerIo->write("Open the following link in your browser:\n$authUrl\n");
        $authCode = trim($composerIo->ask('Enter verification code: '));
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

        file_put_contents($creditionalFullFilename, json_encode($accessToken));
        return "Credentials saved to $creditionalFullFilename\n";
    }

    public function getSavedCredential()
    {
        $creditionalFullFilename = $this->getCreditionalFullFilename();
        if (!file_exists($creditionalFullFilename)) {
            return null;
        }
        $accessToken = json_decode(file_get_contents($creditionalFullFilename), true);
        return $accessToken;
    }

    public function getComposerIo()
    {

        /** init composer IO  */
        $consoleInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        $helperSet = new HelperSet([
            'question' => new QuestionHelper(),
            'formatter' => new FormatterHelper(),
            'descriptor' => new DescriptorHelper(),
            'process' => new ProcessHelper(),
            'debugFormatter' => new DebugFormatterHelper(),
        ]);
        $composerIo = new ConsoleIO($consoleInput, $consoleOutput, $helperSet);
        return $composerIo;
    }

    /**
     * @param $state string crypt token
     */
    public function codeRequest($state)
    {
        // TODO: Implement codeRequest() method.
    }

    /**
     * load saved credential
     * @return array
     */
    public function loadCredential()
    {
        // TODO: Implement loadCredential() method.
    }

    /**
     * Request authCode
     * @param $state
     */
    public function requestAuthCode($state)
    {
        // TODO: Implement requestAuthCode() method.
    }
}
