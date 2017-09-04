<?php

namespace rollun\api\Api\Megaplan\DataStore;

use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataSourceInterface;
use rollun\dic\InsideConstruct;
use Xiag\Rql\Parser\Query;

class MegaplanDataStore extends DataStoreAbstract implements DataSourceInterface
{
    const DEF_ID = 'Id';

    /** @var EntityAbstract */
    protected $singleEntity;

    /** @var EntityAbstract */
    protected $listEntity;

    /**
     * MegaplanDataStore constructor.
     * @param EntityAbstract $singleEntity
     * @param EntityAbstract $listEntity
     */
    public function __construct(EntityAbstract $singleEntity, EntityAbstract $listEntity)
    {
        $this->singleEntity = $singleEntity;
        $this->listEntity = $listEntity;
    }

    public function read($id)
    {
        $this->singleEntity->setId($id);
        return $this->singleEntity->get();
    }

    public function getAll()
    {
        return $this->listEntity->get();
    }

    public function query(Query $query)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }

    public function create($itemData, $rewriteIfExist = false)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }

    public function update($itemData, $createIfAbsent = false)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }

    public function delete($id)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }
}