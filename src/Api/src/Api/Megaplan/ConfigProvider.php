<?php

namespace rollun\api\Api\Megaplan;

use rollun\api\Api\Megaplan\DataStore\MegaplanDataStore;
use rollun\api\Api\Megaplan\Serializer\MegaplanSerializer;
use rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions;
use Megaplan\SimpleClient\Client;
use rollun\api\Api\Megaplan\Entity\Factory\MegaplanClientFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Deals;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Fields;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\FieldsFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Deal;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealFactory;
use rollun\api\Api\Megaplan\DataStore\Factory\MegaplanAbstractFactory;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'dataStore' => $this->getDataStore(),
            'megaplan_entities' => $this->getMegaplanEntities(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
                MegaplanSerializer::class => MegaplanSerializer::class,
                MegaplanSerializerOptions::class => MegaplanSerializerOptions::class,
            ],
            'factories' => [
                Client::class => MegaplanClientFactory::class,
                Deals::class => DealsFactory::class,
                Fields::class => FieldsFactory::class,
                Deal::class => DealFactory::class,
            ],
            'abstract_factories' => [
                MegaplanAbstractFactory::class,
            ],
            'aliases' => [
                'megaplanClient' => Client::class,
                'serializer' => MegaplanSerializer::class,
                'options' => MegaplanSerializerOptions::class,
                'dealsEntity' => Deals::class,
                'dealEntity' => Deal::class,
                'dealListFields' => Fields::class,
                'dataStore' => 'megaplan_deal_dataStore_service',
            ],
            'shared' => [
                'serializer' => false,
                'options' => false,
            ],
        ];
    }

    /**
     * Returns the configuration of the service for the DataStore.
     *
     * This section is a constant and it's strongly not recommended to change anything.
     *
     * @return array
     */
    public function getDataStore()
    {
        return [
            'megaplan_deal_dataStore_service' => [
                'class' => MegaplanDataStore::class,
                'singleEntity' => 'dealEntity',
                'listEntity' => 'dealsEntity',
            ],
        ];
    }

    /**
     * Returns unchanged parameter of the megaplan_entities section.
     *
     * @return array
     */
    public function getMegaplanEntities()
    {
        return [
            'deals' => [
                'dealListFields' => 'dealListFields',
            ],
        ];
    }
}