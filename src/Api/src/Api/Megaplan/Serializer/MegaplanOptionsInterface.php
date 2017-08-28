<?php

namespace rollun\api\Api\Megaplan\Serializer;

interface MegaplanOptionsInterface
{
    /**
     * @return string
     */
    public function getEntity();

    /**
     * @param string $entity
     * @return MegaplanOptions
     */
    public function setEntity($entity);
}