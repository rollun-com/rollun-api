<?php

namespace rollun\api\Api\Google\Gmail;

use rollun\datastore\DataStore\DbTable;
use rollun\dic\InsideConstruct;
use Zend\Db\TableGateway\TableGateway;

class DbDataStore extends DbTable
{

    const DEFAULT_TABLE_NAME = 'gmails';
    const DB_ADAPTER = 'gmailsDbAdapter';
    //
    const MESSAGE_ID = 'id';
    const SUBJECT = 'subject';
    const SENDING_TIME = 'sending_time';
    const FROM = 'from';
    const BODY_HTML = 'body_html';
    const BODY_TXT = 'body_txt';
    const HEADERS = 'headers';

    //
//    const STATUS_IS_PARSED = 'PARSED';
//    const STATUS_IS_NOT_PARSED = 'NOT PARSED';

    public function __construct($gmailsDbAdapter = null)
    {
        //set $this->emailDbAdapter as $cotainer->get('emailDbAdapter');
        $params = InsideConstruct::setConstructParams();

        $dbTable = new TableGateway(static::DEFAULT_TABLE_NAME, $params['gmailsDbAdapter']);
        parent::__construct($dbTable);
    }

    public static function getTableConfig()
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
            static::FROM => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 1024,
                    'nullable' => false
                ]
            ],
            static::SENDING_TIME => [
                'field_type' => 'Integer',
                'field_params' => [
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
            static::HEADERS => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
        ];
    }

}
