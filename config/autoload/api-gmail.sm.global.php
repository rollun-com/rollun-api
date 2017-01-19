<?php

use saas\api\Api\GoogleClientFactory;
use saas\api\Api\GoogleClient;

return [

    'services' => [
        'abstract_factories' => [
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ],
        'factories' => [
            GoogleClient::class => GoogleClientFactory::class,
        ],
        'aliases' => [
            'gmailGoogleClient' => GoogleClient::class,
        ],
    ],
];
