<?php

namespace rollun\test\api\Api\Megaplan\Entity;

use rollun\api\Api\Megaplan\Serializer\MegaplanOptions;
use rollun\datastore\DataStore\DataStoreAbstract;
use Zend\Serializer\Serializer;
use rollun\api\Api\Megaplan\Serializer\Megaplan as MegaplanSerializer;
use Megaplan\SimpleClient\Client;
use Interop\Container\ContainerInterface;
use Mockery;

trait ContainerMockTrait
{
    protected $config = [
        'megaplan' => [
            'api_url' => 'amazon.megaplan.ua',
            'login' => '',
            'password' => '',
        ],
        'megaplan_entities' => [
            'deal_service' => [
                'entity' => 'deal',
                'dataStore' => 'some_dataStore',
                'serializer' => 'serializer',
                'serializerOptions' => 'serializerOptions',
            ],
        ],
    ];

    protected function getContainerMock()
    {
        $containerMock = Mockery::mock(ContainerInterface::class);
        $containerMock->shouldReceive('get')
            ->andReturnUsing(function ($serviceName) {
                switch ($serviceName) {
                    case 'config':
                        $instance = $this->getConfig();
                        break;
                    case 'some_dataStore':
                        $instance = $this->getDataStoreMock();
                        $instance->shouldReceive('create')
                            ->andReturn(true);
                        break;
                    case 'serializer':
                        $instance = $this->getSerializer();
                        break;
                    case 'serializerOptions':
                        $instance = $this->getSerializerOptions();
                        break;
                    case 'megaplan':
                        $instance = $this->getMegaplanClientMock();
                        break;
                    default:
                        throw new \Exception("Can't create service because I'm mock!!");
                        break;
                }
                return $instance;
            });
        return $containerMock;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getDataStoreMock()
    {
        return Mockery::mock(DataStoreAbstract::class);
    }

    protected function getSerializer()
    {
        return Serializer::factory(MegaplanSerializer::class);
    }

    protected function getSerializerOptions()
    {
        return new MegaplanOptions();
    }

    protected function getMegaplanClientMock()
    {
        return Mockery::mock(Client::class);
    }
}