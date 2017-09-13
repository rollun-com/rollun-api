<?php

namespace rollun\api\Api\Megaplan\DataStore;

use rollun\api\Api\Megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\api\Api\Megaplan\Entity\SingeEntityInterface;
use rollun\datastore\DataStore\ConditionBuilder\RqlConditionBuilder;
use rollun\datastore\DataStore\ConditionBuilder\SqlConditionBuilder;
use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataSourceInterface;
use rollun\dic\InsideConstruct;
use Xiag\Rql\Parser\Query;

class MegaplanDataStore extends DataStoreAbstract implements DataSourceInterface
{
    const DEF_ID = 'Id';

    /** @var SingeEntityInterface */
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
        $this->conditionBuilder = new MegaplanConditionBuilder();
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
        $condition = $this->conditionBuilder->__invoke($query->getQuery());
        return $this->listEntity->query($condition);
    }

    public function create($itemData, $rewriteIfExist = false)
    {
        return $this->singleEntity->create($itemData, $rewriteIfExist);
    }

    public function update($itemData, $createIfAbsent = false)
    {
        return $this->singleEntity->update($itemData, $createIfAbsent);
    }

    public function delete($id)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }
}