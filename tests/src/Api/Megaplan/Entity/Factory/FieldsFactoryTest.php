<?php

namespace rollun\test\api\Api\Megaplan\Entity\Factory;

use rollun\api\Api\Megaplan\Entity\Deal\Factory\FieldsFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Fields;
use rollun\test\api\Api\Megaplan\Entity\ContainerMockTrait;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class FieldsFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_fieldsFactory_correctConfig_shouldReturnFieldsObject()
    {
        $factory = new FieldsFactory();
        $instance = $factory($this->getContainerMock(), '');
        $this->assertInstanceOf(
            Fields::class, $instance
        );
        return $factory;
    }

    /**
     * @depends test_fieldsFactory_correctConfig_shouldReturnFieldsObject
     * @param $factory
     */
    public function test_fieldsFactory_dealsSectionAbsents_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']);
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("The configuration for deals entity is not found");
        $factory($this->getContainerMock(), '');
    }

    /**
     * @depends test_fieldsFactory_correctConfig_shouldReturnFieldsObject
     * @param $factory
     */
    public function test_fieldsFactory_filterFieldSectionAbsentsOrNotFilled_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']['filterField']['Program']);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Fields entity has to receive necessary parameter \"Program\" in the \"filterField\" array");
        $factory($this->getContainerMock(), '');
        // The same result will be obtained if I remove whole the 'filterField' part.
    }
}