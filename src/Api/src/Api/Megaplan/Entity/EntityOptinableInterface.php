<?php

namespace rollun\api\Api\Megaplan\Entity;

interface EntityOptinableInterface
{
    /**
     * @param $optionName
     * @return mixed
     */
    public function getOption($optionName);

    /**
     * @param $optionName
     * @param $optionValue
     * @return EntityAbstract
     */
    public function setOption($optionName, $optionValue);
}