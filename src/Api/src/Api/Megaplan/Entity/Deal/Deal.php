<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Entity\SingleEntityAbstract;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class Deal extends SingleEntityAbstract
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/card.api';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const ENTITY_DATA_KEY = 'deal';

    /**
     * The list of fields which can be on top level an array of created/updated entity.
     * No other fields can be here.
     *
     * @var array
     */
    protected $allowedTopLevelDataFields = [
        self::ID_OPTION_KEY,
        'ProgramId',
        'StatusId',
        'StrictLogic',
        'Model',
        'Positions',
    ];

    /**
     * Requested fields (changes the default set of fields)
     *
     * @var array
     */
    protected $requestedFields;

    /**
     * Extra fields (adds the default set of fields)
     *
     * @var array
     */
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

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
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

    /**
     * Sends request to Megaplan and returns created or updated entity accordingly to DataStore interface.
     *
     * @param $itemData
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function put($itemData)
    {
        $this->checkDataStructure($itemData);

        $response = $this->megaplanClient->get('/BumsTradeApiV01/Deal/save.api', $itemData);
        $data = $this->serializer->unserialize($response);

        $dealId = $data[static::ID_OPTION_KEY];
        $this->setId($dealId);
        return $this->get();
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if ($this->has($itemData[static::ID_OPTION_KEY]) && !$rewriteIfExist) {
                throw new InvalidArgumentException("Can't create a new deal because the deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" exists but you didn't allow its rewriting.");
            }
        }
        return $this->put($itemData);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function update($itemData, $createIfAbsent = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if (!$this->has($itemData[static::ID_OPTION_KEY]) && !$createIfAbsent) {
                throw new InvalidArgumentException("The deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" doesn't exist.");
            }
        }
        return $this->put($itemData);
    }
}