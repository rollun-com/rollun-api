<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.01.17
 * Time: 18:00
 */
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\api\Api\Gmail\DbDataStore as GmailDbDataStore;

return [
    ApiGoogleClientAbstractFactory::KEY_GOOGLE_API_CLIENTS => [
        'gmailGoogleClient' => [
            AbstractFactoryAbstract::KEY_CLASS => ApiGoogleClientCli::class, //optionaly
            ApiGoogleClientAbstractFactory::KEY_SCOPES => [ //Must be set:
                Google_Service_Gmail::GMAIL_READONLY,
            ],
            ApiGoogleClientAbstractFactory::KEY_CONFIG => [
                //Must be set:
                "login_hint" => "test.rocky.gift@gmail.com", //<<--imortant!!!  will be use as user's Email
                //optionaly:
                "application_name" => "Gmail Parser",
                "approval_prompt" => "auto",
            ],
        ]
    ],
    'services' => [
        'abstract_factories' => [
            ApiGoogleClientFactoryAbstractFactory::class,
        ],
        'aliases' => [
            GmailDbDataStore::DB_ADAPTER => 'db',
        ],
    ],
];
