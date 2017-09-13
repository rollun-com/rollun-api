<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\api\Api\Megaplan\Entity\SingeEntityInterface;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class Deal extends EntityAbstract implements SingeEntityInterface
{
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/card.api';

    const ENTITY_DATA_KEY = 'deal';

    const ID_OPTION_KEY = 'Id';

    protected $allowedTopLevelDataFields = [
        self::ID_OPTION_KEY,
        'ProgramId',
        'StatusId',
        'StrictLogic',
        'Model',
        'Positions',
    ];

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
        return $this;
    }

    protected function isExists($id)
    {
        $this->setId($id);
        // If the deal doesn't exist here an exception will be thrown
        $this->get();
        // if it wasn't then just return true
        return true;
    }

    protected function checkDataStructure($itemData)
    {
        foreach (array_keys($itemData) as $field) {
            if (!in_array($field, $this->allowedTopLevelDataFields)) {
                throw new InvalidArgumentException("Can't process the deal with field \"{$field}\".");
            }
        }
    }


    protected function put($itemData)
    {
        $this->checkDataStructure($itemData);

        $response = $this->megaplanClient->get('/BumsTradeApiV01/Deal/save.api', $itemData);
        $data = $this->serializer->unserialize($response);

        $dealId = $data[static::ID_OPTION_KEY];
        $this->setId($dealId);
        return $this->get();
    }

    public function create($itemData, $rewriteIfExist = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if ($this->isExists($itemData[static::ID_OPTION_KEY]) && !$rewriteIfExist) {
                throw new InvalidArgumentException("Can't create a new deal because the deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" exists but you didn't allow its rewriting.");
            }
        }
        return $this->put($itemData);
    }

    public function update($itemData, $createIfAbsent = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if (!$this->isExists($itemData[static::ID_OPTION_KEY]) && !$createIfAbsent) {
                throw new InvalidArgumentException("The deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" doesn't exist.");
            }
        }
        return $this->put($itemData);
    }
}