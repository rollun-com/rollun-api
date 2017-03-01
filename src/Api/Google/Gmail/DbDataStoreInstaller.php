<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace rollun\api\Api\Google\Gmail;

use Zend\Db\Adapter\AdapterInterface;
use rollun\datastore\TableGateway\TableManagerMysql as TableManager;
use rollun\dic\InsideConstruct;
use rollun\api\Api\Google\Gmail\DbDataStore;
use rollun\installer\Install\InstallerAbstract;
use Interop\Container\ContainerInterface;
use Composer\IO\IOInterface;

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
        $tableManager = new TableManager($this->gmailsDbAdapter);
        $tableConfig = DbDataStore::getTableConfig();
        $tableName = DbDataStore::DEFAULT_TABLE_NAME;
        $tableManager->createTable($tableName, $tableConfig);
    }

    public function uninstall()
    {

    }

    public function reinstall()
    {

    }

}
