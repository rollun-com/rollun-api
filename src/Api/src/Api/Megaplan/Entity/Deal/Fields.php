<?php

namespace rollun\api\Api\Megaplan\Entity\Deal;

use rollun\api\Api\Megaplan\Entity\ListEntityAbstract;

class Fields extends ListEntityAbstract
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/listFields.api';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const ENTITY_DATA_KEY = 'Fields';

    const PROGRAM_ID_KEY = 'ProgramId';

    /**
     * The only required parameter
     *
     * @var int
     */
    protected $programId;

    /**
     * Fields constructor.
     * @param $programId
     */
    public function __construct($programId)
    {
        parent::__construct();
        $this->programId = $programId;
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    protected function prepareRequestParams()
    {
        return [
            self::PROGRAM_ID_KEY => $this->programId,
        ];
    }
}