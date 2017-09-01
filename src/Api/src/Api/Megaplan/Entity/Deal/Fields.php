<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class Fields extends EntityAbstract
{
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/listFields.api';

    const ENTITY_DATA_KEY = 'Fields';

    const PROGRAM_ID_KEY = 'ProgramId';

    protected $programId;

    protected function prepareRequestParams()
    {
        if (is_null($this->programId)) {
            throw new InvalidArgumentException("The required option \"" . self::PROGRAM_ID_KEY . "\" is not set.");
        }
        return [
            self::PROGRAM_ID_KEY => $this->programId,
        ];
    }

    public function getOption($optionName)
    {
        if (self::PROGRAM_ID_KEY != $optionName) {
            throw new InvalidArgumentException("Specified option \"{$optionName}\" is not allowed in the class \""
                . __CLASS__ . "\". Only one with name \"" . self::PROGRAM_ID_KEY . "}\" is allowed.");
        }
        return $this->programId;
    }

    public function setOption($optionName, $optionValue)
    {
        if (self::PROGRAM_ID_KEY != $optionName) {
            throw new InvalidArgumentException("Specified option \"{$optionName}\" is not allowed in the class \""
                . __CLASS__ . "\". Only one with name \"" . self::PROGRAM_ID_KEY . "}\" is allowed.");
        }
        $this->programId = $optionValue;
    }
}