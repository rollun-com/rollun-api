<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Exception\InvalidRequestCountException;
use rollun\api\Api\Megaplan\Entity\EntityAbstract;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory;

/**
 * Class Deals
 *
 * Allows to receive a list of deals.
 * Note: This entity doesn't allow to receive single deal. To receive one you have to use Deal::class.
 *
 * @package rollun\api\Api\Megaplan\Entity
 */
class Deals extends EntityAbstract
{
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/list.api';

    const ENTITY_DATA_KEY = 'deals';

    const MAX_REQUEST_COUNT_PRE_HOUR = 3000;

    /** @var Fields */
    protected $dealListFields;

    /**
     * TODO: Как устанавливать фильтруемые поля? Через установку опций не получится. Еще один интерфейс делать?
     * @var array
     */
    protected $filterFields;

    protected $requestedFields;

    protected $extraFields;

    protected $requestParams;

    /**
     * Deals constructor.
     * @param Fields $dealListFields
     * @param array $filterFields
     * @param array $requestedFields
     * @param array $extraFields
     * @throws \rollun\api\Api\Megaplan\Exception\InvalidArgumentException
     */
    public function __construct(Fields $dealListFields,
                                array $filterFields,
                                array $requestedFields = [],
                                array $extraFields = [])
    {
        parent::__construct();
        $this->dealListFields = $dealListFields;
        $this->filterFields = $filterFields;
        $this->requestedFields = $requestedFields;
        $this->extraFields = $extraFields;
    }

    public function get()
    {
        $data = [];
        $requestCount = 0;
        do {
            $data = array_merge($data, parent::get());

            $requestCount++;
            if ($requestCount >= self::MAX_REQUEST_COUNT_PRE_HOUR) {
                throw new InvalidRequestCountException("The limit of requests per hour is exceeded");
            }
            usleep($this->getRequestInterval());
            $this->requestParams['Offset'] += static::MAX_LIMIT;
        } while(count($data) == $this->requestParams['Offset']);
        return $data;
    }

    public function query($condition)
    {
        $this->filterFields = array_merge($this->filterFields, $condition);
        return $this->get();
    }

    protected function prepareRequestParams()
    {
        if (!count($this->requestParams)) {
            $this->requestParams = [
                'FilterFields' => $this->filterFields,
                'RequestedFields' => $this->getRequestedFields(),
                'ExtraFields' => $this->getExtraFields(),
                'Limit' => static::MAX_LIMIT,
                'Offset' => 0,
            ];
        }
        return $this->requestParams;
    }

    protected function getExtraFields()
    {
        if (!count($this->extraFields)) {
            $fields = $this->dealListFields->get();
            foreach ($fields as $field) {
                if (preg_match("/CustomField/", $field['Name'])) {
                    $this->extraFields[] = $field['Name'];
                }
            }
        }
        return $this->extraFields;
    }

    protected function getRequestedFields()
    {
        return $this->requestedFields;
    }

    protected function getRequestInterval()
    {
        return ceil(3600 / self::MAX_REQUEST_COUNT_PRE_HOUR * 1000);
    }
}