<?php

namespace rollun\api\Api\Megaplan\Serializer;

use Ramsey\Uuid\Uuid;
use rollun\dic\InsideConstruct;
use Zend\Serializer\Adapter\Json;

class MegaplanSerializer extends Json
{
    public function __construct($options = null)
    {
        InsideConstruct::init();
        parent::__construct($this->options);
    }

    /**
     * Deserialize an incoming JSON-string to array.
     *
     * The incoming string is JSON-string in a Megaplan response format which has the following view:
     * Array
     * (
     *     [status] => Array
     *         (
     *             [code] => ok
     *             [message] =>
     *         )
     *
     *     [data] => Array
     *         (
     *             [deals] => Array
     *                 (
     *                     [0] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                     [1] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                     // ...
     *                     [N] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                 )
     *         )
     * )
     * Surely the data in this format is not writable to DataStore.
     * So this serializer based on Zend\Serializer\Adapter\Json just extract the raw data from scope.
     * And then create outcoming array the following view:
     * Array
     * (
     *     ['id'] => $id,
     *     ['deal'] => json_encode(['deal']),
     * )
     * That's all. No other changes are made to the data.
     *
     * @param string $serialized
     * @return array
     */
    public function unserialize($serialized)
    {
        // Data may come already in stdClass view
        if (!(is_string($serialized) && (is_object(json_decode($serialized)) || is_array(json_decode($serialized))))) {
            // So encode them again
            $serialized = parent::serialize($serialized);
        }
        // Now decode data with $assoc = true
        $unserializedData = parent::unserialize($serialized);
        $rawUnserializedData = $unserializedData["data"][$this->options->getEntity()];
        return $rawUnserializedData;

//        $returnData = [];
//        foreach ($rawUnserializedData as $entity) {
//            $id = isset($entity['Id']) ? $entity['Id'] : Uuid::uuid4()->__toString();
//            $returnData[] = [
//                'id' => $id,
//                $this->options->getEntity() => $this->serialize($entity),
//            ];
//        }
//        return $returnData;
    }
}