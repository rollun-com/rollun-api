<?php

namespace rollun\api\Api\Megaplan\DataStore\ConditionBuilder;

use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\datastore\DataStore\ConditionBuilder\RqlConditionBuilder;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

class MegaplanConditionBuilder extends ConditionBuilderAbstract
{
    protected $literals = [
        'LogicOperator' => [
            'and' => ['before' => '(', 'between' => ' AND ', 'after' => ')'],
            'or' => ['before' => '(', 'between' => ' OR ', 'after' => ')'],
            'not' => ['before' => '( NOT (', 'between' => ' error ', 'after' => ') )'],
        ],
//        'ArrayOperator' => [
//            'in' => ['before' => '(', 'between' => ' IN (', 'delimiter' => ',', 'after' => '))'],
//            'out' => ['before' => '(', 'between' => ' NOT IN (', 'delimiter' => ',', 'after' => '))']
//        ],
        'ScalarOperator' => [
            // {"TimeUpdated":"2017-09-01 00:00:00"}
            'eq' => ['before' => '{"', 'between' => '":"', 'after' => '"}'],
            'ne' => ['before' => '[', 'between' => '<>', 'after' => ']'],
            // {"TimeUpdated":{"greaterOrEqual":"2017-09-01 00:00:00"}}
            'ge' => ['before' => '{"', 'between' => '":{"greaterOrEqual":"', 'after' => '"}}'],
            'gt' => ['before' => '[', 'between' => '>', 'after' => ']'],
            'le' => ['before' => '[', 'between' => '<=', 'after' => ']'],
            'lt' => ['before' => '[', 'between' => '<', 'after' => ']'],
        ]
    ];

    public static function encodeString($value)
    {
        return $value;
    }

    public function __invoke(AbstractQueryNode $rootQueryNode = null)
    {
        return json_decode(parent::__invoke($rootQueryNode), true);
    }
}