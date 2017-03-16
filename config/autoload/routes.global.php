<?php

use rollun\api\Api\HelloActionFactory;

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
        'factories' => [
        ],
    ],
    'routes' => [

    ],
];
