<?php

namespace rollun\api\Api\Megaplan\Serializer;

interface MegaplanSerializerOptionsInterface
{
    /**
     * @return string
     */
    public function getEntity();

    /**
     * @param string $entity
     * @return MegaplanSerializerOptions
     */
    public function setEntity($entity);
}