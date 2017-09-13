<?php

namespace rollun\api\Api\Megaplan\Entity;

interface SingeEntityInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return SingeEntityInterface
     */
    public function setId($id);

    /**
     * @param $itemData
     * @param bool|false $rewriteIfExist
     * @return array
     */
    public function create($itemData, $rewriteIfExist = false);

    /**
     * @param $itemData
     * @param bool|false $createIfAbsent
     * @return array
     */
    public function update($itemData, $createIfAbsent = false);

}