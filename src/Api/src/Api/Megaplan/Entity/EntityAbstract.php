<?php

namespace rollun\api\Api\Megaplan\Entity;

use Megaplan\SimpleClient\Client;
use rollun\api\Api\Megaplan\Serializer\MegaplanOptionsInterface;
use Zend\Serializer\Adapter\AdapterInterface as SerializerAdapterInterface;
use rollun\datastore\DataStore\DataStoreAbstract;

class EntityAbstract
{
    const URI_ENTITY_GET = '';

    const ENTITY_LIST_KEY = '';

    const MAX_LIMIT = 100;

    const REQUESTED_FIELDS = [];

    const EXTRA_FIELDS = [];

    /** @var Client */
    protected $megaplanClient;

    /** @var DataStoreAbstract */
    protected $dataStore;

    /** @var SerializerAdapterInterface */
    protected $serializer;

    /**
     * EntityAbstract constructor.
     * @param Client $megaplanClient
     * @param DataStoreAbstract $dataStore
     * @param SerializerAdapterInterface $serializer
     */
    public function __construct(Client $megaplanClient, DataStoreAbstract $dataStore, SerializerAdapterInterface $serializer)
    {
        $this->megaplanClient = $megaplanClient;
        $this->dataStore = $dataStore;
        $this->serializer = $serializer;
        if ($this->serializer->getOptions() instanceof MegaplanOptionsInterface) {
            $this->serializer->getOptions()->setEntity(static::ENTITY_LIST_KEY);
        }
    }

    public function get()
    {
        $data = [];
        $requestParams = $this->prepareRequestParams();
        do {
            $response = $this->megaplanClient->get(static::URI_ENTITY_GET, $requestParams);
            $data = array_merge($data, $this->serializer->unserialize($response));

            $requestParams['Offset'] += static::MAX_LIMIT;
        } while(count($data) == $requestParams['Offset']);

        $this->dataStore->create($data, true);
        return $data;
    }

    protected function prepareRequestParams()
    {
        $params = [
            'RequestedFields' => static::REQUESTED_FIELDS,
            'ExtraFields' => static::EXTRA_FIELDS,
            'Limit' => static::MAX_LIMIT,
            'Offset' => 0,
        ];
        return $params;
    }
}