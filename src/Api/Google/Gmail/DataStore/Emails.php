<?php

namespace rollun\api\Api\Gmail\DataStore;

use rollun\datastore\DataStore\DbTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use zaboy\res\Di\InsideConstruct;
use rollun\datastore\TableGateway\TableManagerMysql as TableManager;

class Emails extends DbTable
{

    const DB_ADAPTER = 'emailsDbAdapter';
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

    public static function install($tableName)
    {
        $tableManager = new TableManager($this->dbTable->adapter);
        $tableConfig = $this->getTableConfig();
        $tableManager->createTable($tableName, $tableConfig);
    }

    public static function getDbAdapterServiceName()
    {
        return static::DB_ADAPTER;
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
