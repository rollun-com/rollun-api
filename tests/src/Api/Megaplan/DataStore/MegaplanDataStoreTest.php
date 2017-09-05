<?php

namespace rollun\test\api\Api\Megaplan\DataStore;

use rollun\api\Api\Megaplan\Entity\Deal\Deal;
use rollun\api\Api\Megaplan\Entity\Deal\Deals;

class MegaplanDataStoreTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_read_shouldRunSingleEntityGet()
    {
        $megaplanDataStore = $this->getContainerMock()->get($this->serviceName);
        $this->assertEquals(
            Deal::class . '::' . 'get', $megaplanDataStore->read(1)
        );
    }

    public function test_getAll_shouldRunListEntityGet()
    {
        $megaplanDataStore = $this->getContainerMock()->get($this->serviceName);
        $this->assertEquals(
            Deals::class . '::' . 'get', $megaplanDataStore->getAll()
        );
    }
}