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

    /**
     * Fields constructor.
     * @param $programId
     */
    public function __construct($programId)
    {
        parent::__construct();
        $this->programId = $programId;
    }

    protected function prepareRequestParams()
    {
        return [
            self::PROGRAM_ID_KEY => $this->programId,
        ];
    }
}