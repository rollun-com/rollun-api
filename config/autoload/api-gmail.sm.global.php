<?php

use saas\api\Api\GoogleClientFactory;
use saas\api\Api\GoogleClient;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;

return [

    'services' => [
        'abstract_factories' => [
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
            ApiGoogleClientAbstractFactory::class
        ],
        'factories' => [
        ],
        'aliases' => [
            'gmailGoogleClient' => ApiGoogleClientCli::class,
        ],
    ],
    ApiGoogleClientAbstractFactory::KEY_GOOGLE_API_CLIENTS => [
        ApiGoogleClientCli::class => [
            "CliClient" => [
                AbstractFactoryAbstract::KEY_CLASS => GoogleClient::class, //optionaly
                ApiGoogleClientAbstractFactory::KEY_SCOPES => [ //Must be set:
                    Google_Service_Gmail::GMAIL_READONLY,
                ],
                ApiGoogleClientAbstractFactory::KEY_CONFIG => [
                    //Must be set:
                    "login_hint" => "test.rocky.gift@gmail.com", //<<--imortant!!!  will be use as user's Email
                    //optionaly:
                    "application_name" => "Gmail Parser",
                    "approval_prompt" => "Just do it!",
                ],
            ],
        ]
    ]
];
