<?php

namespace rollun\api\Api\Google\Client;

use rollun\installer\Install\InstallerInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;
use rollun\api\Api\Gmail\DbDataStore as GmailDbDataStore;

/**
 * vendor\bin\InstallerSelfCall.bat "rollun\api\Api\Google\Client\CliInstaller" install
 */
class CliInstaller extends InstallerAbstract implements InstallerInterface
{

    public function install()
    {
        $gmailDbAdapterServiceName = GmailDbDataStore::getDbAdapterServiceName();
        $gmailDbAdapterService = $this->container->get($gmailDbAdapterServiceName);

        $tableManager = new TableManager($gmailDbAdapterService);
        $tableConfig = GmailDbDataStore::DB_ADAPTER;
        $tableName = GmailDbDataStore::DEFAULT_TABLE_NAME;
        $tableManager->createTable($tableName, $tableConfig);


        $tableManager = new TableManager($this->emailDbAdapter);
        $tableConfig = GmailDbDataStore::getTableConfig();
        $tableName = Email::TABLE_NAME;
        $tableManager->createTable($tableName, $tableConfig);
    }

    public function uninstall()
    {

    }

    public function reinstall()
    {

    }

}
