<?php

namespace rollun\test\api\Api\Megaplan\DataStore;

use Mockery;
use Interop\Container\ContainerInterface;
use rollun\api\Api\Megaplan\DataStore\Factory\MegaplanAbstractFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Deal;
use rollun\api\Api\Megaplan\Entity\Deal\Deals;

trait ContainerMockTrait
{
    protected $serviceName = 'megaplan_deal_dataStore_service';
    protected $config;

    protected function setUp()
    {
        // I have to do this because megaplan entities use InsideConstructor which builds $container from real config.
        global $container;
        $container = $this->getContainerMock();

        $this->config = [
            'dataStore' => [
                $this->serviceName => [
                    'singleEntity' => 'dealEntity',
                    'listEntity' => 'dealsEntity',
                    'class' => \rollun\api\Api\Megaplan\DataStore\MegaplanDataStore::class,
                ],
            ],
        ];
    }

    protected function getContainerMock()
    {
        $containerMock = Mockery::mock(ContainerInterface::class);
        $containerMock->shouldReceive('get')
            ->andReturnUsing(function ($serviceName) {
                switch ($serviceName) {
                    case 'config':
                        $instance = $this->getConfig();
                        break;
                    case 'dealEntity':
                        $instance = Mockery::mock(Deal::class);
                        $instance->shouldReceive('get')
                            ->andReturn(Deal::class . '::' . 'get');
                        $instance->shouldReceive('setId')
                            ->andReturn(true);
                        break;
                    case 'dealsEntity':
                        $instance = Mockery::mock(Deals::class);
                        $instance->shouldReceive('get')
                            ->andReturn(Deals::class . '::' . 'get');
                        break;
                    case $this->serviceName:
                        $factory = new MegaplanAbstractFactory();
                        $instance = $factory($this->getContainerMock(), $this->serviceName);
                        break;
                    default:
                        throw new \Exception("Can't create service because I'm mock!!");
                        break;
                }
                return $instance;
            });
        $containerMock->shouldReceive('has')
            ->andReturn(true);
        return $containerMock;
    }

    protected function getConfig()
    {
        return $this->config;
    }
}