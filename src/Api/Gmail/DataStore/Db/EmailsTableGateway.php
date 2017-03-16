<?php

namespace rollun\api\Api\Gmail\DataStore;

use rollun\datastore\TableGateway\TableManagerMysql as TableManager;
use rollun\dic\InsideConstruct;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class EmailsTableGateway extends TableGateway
{

    const TABLE_NAME = 'gmails';
    //
    const MESSAGE_ID = 'id';
    const SUBJECT = 'subject';
    const SENDING_TIME = 'sending_time';
    const FROM = 'from';
    const BODY_HTML = 'body_html';
    const BODY_TXT = 'body_txt';
    //
    const STATUS_IS_PARSED = 'PARSED';
    const STATUS_IS_NOT_PARSED = 'NOT PARSED';

    /**
     *
     * @var AdapterInterface
     */
    protected $emailsDbAdapter;

    public function __construct($emailsDbAdapter = null)
    {
        //set $this->emailDbAdapter as $cotainer->get('emailDbAdapter');
        InsideConstruct::initServices();

        $dbTable = new TableGateway(static::TABLE_NAME, $this->emailsDbAdapter);
        parent::__construct($dbTable);
    }

    public function install()
    {
        $tableManager = new TableManager($this->emailsDbAdapter);
        $tableConfig = $this->getTableConfig();
        $tableName = static::TABLE_NAME;
        $tableManager->createTable($tableName, $tableConfig);
    }

    protected function getTableConfig()
    {
        return [
            static::MESSAGE_ID => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 32,
                    'nullable' => false
                ]
            ],
            static::SUBJECT => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 4094,
                    'nullable' => true
                ]
            ],
            static::SENDING_TIME => [
                'field_type' => 'Integer',
                'field_params' => [
                    'nullable' => true
                ]
            ],
            static::FROM => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 256,
                    'nullable' => true
                ]
            ],
            static::BODY_HTML => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
            static::BODY_TXT => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
        ];
    }

}
