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

    protected $requestedFields;

    protected $extraFields;

    /**
     * Deal constructor.
     * @param array $requestedFields
     * @param array $extraFields
     */
    public function __construct(array $requestedFields = [], array $extraFields = [])
    {
        parent::__construct();
        $this->requestedFields = $requestedFields;
        $this->extraFields = $extraFields;
    }

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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}