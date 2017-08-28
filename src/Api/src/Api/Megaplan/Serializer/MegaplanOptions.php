<?php

namespace rollun\api\Api\Megaplan\Serializer;

use Zend\Serializer\Adapter\JsonOptions;
use Zend\Serializer\Exception\InvalidArgumentException;

class MegaplanOptions extends JsonOptions implements MegaplanOptionsInterface
{
    protected $entity;

    /**
     * @return string
     */
    public function getEntity()
    {
        if (is_null($this->entity)) {
            throw new InvalidArgumentException("Required option \"entity\" for Megaplan serializer is not set.");
        }
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return MegaplanOptions
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
}