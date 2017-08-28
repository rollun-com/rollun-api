<?php

namespace rollun\api\Api\Megaplan\Entity;

class Deal extends EntityAbstract
{
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/list.api';

    const ENTITY_LIST_KEY = 'deals';

    const REQUESTED_FIELDS = [
        'Id',
        'Name',
        'Program',
        'TimeCreated',
        'Positions',
        'FinalPrice',
        'Owner',
        'Manager',
    ];

    const EXTRA_FIELDS = [
        'Category1000051CustomFieldDataZakupki',
        'Category1000051CustomFieldPostavshchik',
        'Category1000051CustomFieldShipmentId',
    ];
}