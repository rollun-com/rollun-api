<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace rollun\api\Api\Google\Gmail;

use Composer\IO\IOInterface;
use Interop\Container\ContainerInterface;
use rollun\datastore\TableGateway\TableManagerMysql as TableManager;
use rollun\dic\InsideConstruct;
use rollun\installer\Install\InstallerAbstract;
use Zend\Db\Adapter\AdapterInterface;

/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
class DbDataStoreInstaller extends InstallerAbstract
{

    /**
     *
     * @var AdapterInterface
     */
    private $gmailsDbAdapter;

    public function __construct(ContainerInterface $container, IOInterface $ioComposer, AdapterInterface $gmailsDbAdapter = null)
    {
        //set $this->gmailsDbAdapter as $cotainer->get('gmailsDbAdapter');
        InsideConstruct::init();
    }

    public function install()
    {
        if (isset($this->gmailsDbAdapter)) {
            /** @noinspection PhpParamsInspection */
            $tableManager = new TableManager($this->gmailsDbAdapter);
            $tableConfig = DbDataStore::getTableConfig();
            $tableName = DbDataStore::DEFAULT_TABLE_NAME;
            $tableManager->createTable($tableName, $tableConfig);
        }
    }

    public function uninstall()
    {

    }

    public function reinstall()
    {

    }

}
