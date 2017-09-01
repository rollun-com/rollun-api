<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class Deal extends EntityAbstract
{
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/card.api';

    const ENTITY_DATA_KEY = 'deal';

    const ID_OPTION_KEY = 'Id';

    protected $id;

    protected $requestedFields = [];

    protected $extraFields = [];

    protected function prepareRequestParams()
    {
        if (is_null($this->id)) {
            throw new InvalidArgumentException("The required option \"" . self::ID_OPTION_KEY . "\" is not set.");
        }
        $requestParams = [
            self::ID_OPTION_KEY => $this->id,
            'RequestedFields' => $this->requestedFields,
            'ExtraFields' => $this->extraFields,
        ];
        return $requestParams;
    }

    public function getOption($optionName)
    {
        return $this->determineInsideParameter($optionName);
    }

    public function setOption($optionName, $optionValue)
    {
        $param = &$this->determineInsideParameter($optionName);
        $param = $optionValue;
    }

    protected function &determineInsideParameter($optionName)
    {
        switch (true) {
            case (self::ID_OPTION_KEY == $optionName):
                $param = &$this->id;
                break;
            case (preg_match("/CustomField/", $optionName)):
                $param = &$this->extraFields[$optionName];
                break;
            default:
                $param = &$this->requestedFields[$optionName];
                break;
        }
        return $param;
    }
}