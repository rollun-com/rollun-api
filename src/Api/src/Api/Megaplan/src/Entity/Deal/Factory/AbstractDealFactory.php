<?php


namespace rollun\api\Api\Megaplan\Entity\Deal\Factory;
use rollun\api\Api\Megaplan\Entity\Factory\AbstractFactory;


class AbstractDealFactory extends AbstractFactory
{
    const DEALS_KEY = 'deals';
    const DEAL_LIST_FIELDS_KEY = 'dealListFields';
    const FILTER_FIELD_KEY = 'filterField';
    const FILTER_FIELD_PROGRAM_KEY = 'Program';
    const REQUESTED_FIELDS_KEY = 'requestedFields';
    const EXTRA_FIELDS_KEY = 'extraFields';
}