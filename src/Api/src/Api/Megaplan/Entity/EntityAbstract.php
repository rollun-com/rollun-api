<?php

namespace rollun\api\Api\Megaplan\Entity;

use Megaplan\SimpleClient\Client;
use rollun\api\Api\Megaplan\Serializer\MegaplanOptionsInterface;
use rollun\dic\InsideConstruct;
use Zend\Serializer\Adapter\AdapterInterface as SerializerAdapterInterface;
use rollun\datastore\DataStore\DataStoreAbstract;

abstract class EntityAbstract
{
    const URI_ENTITY_GET = '';

    const ENTITY_DATA_KEY = '';

    const MAX_LIMIT = 100;

    /** @var Client */
    protected $megaplanClient;

    /** @var DataStoreAbstract */
    protected $dataStore;

    /** @var SerializerAdapterInterface */
    protected $serializer;

    /**
     * EntityAbstract constructor.
     * @param Client $megaplanClient
     * @param SerializerAdapterInterface $serializer
     */
    public function __construct(Client $megaplanClient = null, SerializerAdapterInterface $serializer = null)
    {
        InsideConstruct::setConstructParams();
        if ($this->serializer->getOptions() instanceof MegaplanOptionsInterface) {
            $this->serializer->getOptions()->setEntity(static::ENTITY_DATA_KEY);
        }
    }

    public function get()
    {
        $requestParams = $this->prepareRequestParams();
        $response = $this->megaplanClient->get(static::URI_ENTITY_GET, $requestParams);
        $data = $this->serializer->unserialize($response);
        return $data;
    }

    abstract protected function prepareRequestParams();
}