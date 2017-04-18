<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace rollun\api\Api\Google\Gmail;

use Composer\IO\IOInterface;
use Google_Service_Gmail;
use Interop\Container\ContainerInterface;
use rollun\api\Api\Google\Client\GoogleClientInstaller;
use rollun\datastore\TableGateway\TableManagerMysql as TableManager;
use rollun\dic\InsideConstruct;
use rollun\installer\Install\InstallerAbstract;
use rollun\utils\DbInstaller;
use Zend\Db\Adapter\AdapterInterface;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Gmail\DbDataStore as GmailDbDataStore;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;


/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
class GmailClientInstaller extends InstallerAbstract
{
    /**
     *
     * @var AdapterInterface
     */
    private $gmailsDbAdapter;

    public function install()
    {
        $this->gmailsDbAdapter = $this->container->get('db');

        if (isset($this->gmailsDbAdapter)) {
            /** @noinspection PhpParamsInspection */
            $tableManager = new TableManager($this->gmailsDbAdapter);
            $tableConfig = DbDataStore::getTableConfig();
            $tableName = DbDataStore::DEFAULT_TABLE_NAME;
            if (!$tableManager->hasTable($tableName)) {
                $tableManager->createTable($tableName, $tableConfig);
            } else {
                $this->consoleIO->write("Table $tableName is already exist.");
            }
        }
        do {
            $userEmail = $this->consoleIO->ask("set user email:\n");
            if (is_null($userEmail)) {
                $this->consoleIO->write("User email not set!");
            }
        } while (is_null($userEmail));

        return [
            ApiGoogleClientAbstractFactory::KEY_GOOGLE_API_CLIENTS => [
                'gmailGoogleClient' => [
                    AbstractFactoryAbstract::KEY_CLASS => ApiGoogleClientCli::class, //optionaly
                    ApiGoogleClientAbstractFactory::KEY_SCOPES => [ //Must be set:
                        Google_Service_Gmail::GMAIL_READONLY,
                    ],
                    ApiGoogleClientAbstractFactory::KEY_CONFIG => [
                        //Must be set:
                        "login_hint" => $userEmail, //<<--imortant!!!  will be use as user's Email
                        //optionaly:
                        "application_name" => "Gmail Parser",
                        "approval_prompt" => "auto",
                    ],
                ]
            ],
            'dependencies' => [
                'aliases' => [
                    GmailDbDataStore::DB_ADAPTER => 'db', //gmailsDbAdapter => 'db'
                ],
            ],
        ];
    }

    public function getDependencyInstallers()
    {
        return [
            GoogleClientInstaller::class,
            DbInstaller::class,
        ];
    }

    public function isInstall()
    {
        $config = $this->container->get('config');
        return (
            isset($config[ApiGoogleClientAbstractFactory::KEY_GOOGLE_API_CLIENTS]['gmailGoogleClient']) &&
            isset($config['dependencies']['aliases'][GmailDbDataStore::DB_ADAPTER]) &&
            $this->container->has('gmailGoogleClient')
        );
    }

    public function uninstall()
    {
        $this->gmailsDbAdapter = $this->container->get('db');
        if (isset($this->gmailsDbAdapter)) {
            /** @noinspection PhpParamsInspection */
            $tableManager = new TableManager($this->gmailsDbAdapter);
            $tableName = DbDataStore::DEFAULT_TABLE_NAME;
            if ($tableManager->hasTable($tableName)) {
                $tableManager->deleteTable($tableName);
            }
        }
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
                $description = "Создает gmail client. И хранилище для него.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }
}
