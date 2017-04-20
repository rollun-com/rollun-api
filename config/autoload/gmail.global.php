<?php

use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\RouterInterface;
use rollun\api\Api\Google\Gmail\MessagesList;
use rollun\api\Api\Google\Gmail\MessagesListAbstractFactory;
use rollun\api\App\MessagesListAction;
use rollun\api\App\MessagesListActionFactory;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ClientAbstractFactory;

return [
    'dependencies' => [
        'invokables' => [
        //RouterInterface::class => FastRouteRouter::class,
        ],
        'abstract_factories' => [
            MessagesListAbstractFactory::class,
            ClientAbstractFactory::class
        ],
        'factories' => [
            MessagesListAction::class => MessagesListActionFactory::class,
        ]
    ],
    //
    'MESSAGES_LIST' => [
        'test_message_list' => [
            MessagesListAbstractFactory::KEY_CLASS => MessagesList::class,
            MessagesListAbstractFactory::KEY_GOOGLE_API_CLIENT => 'gmailGoogleClient',
            MessagesListAbstractFactory::KEY_OPT_PARAM => [
                'labelIds' => "INBOX",
                'q' => 'filename:(jpg OR png OR gif)', //'!in:chats' https://support.google.com/mail/answer/7190?hl=en&ref_topic=3394914
            ]
        ]
    ],
    //
    'GOOGLE_API_CLIENTS' => [
        'gmailGoogleClient' => [
            'class' => 'rollun\api\Api\Google\Client\Cli',
            'SCOPES' => [
                'https://www.googleapis.com/auth/gmail.readonly',
            ],
            'CONFIG' => [
                'login_hint' => 'test.rocky.gift@gmail.com',
                'application_name' => 'Gmail Parser',
                'approval_prompt' => 'auto',
            ],
        ],
    ],
];
