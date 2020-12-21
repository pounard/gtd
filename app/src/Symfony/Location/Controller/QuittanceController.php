<?php

declare(strict_types=1);

namespace Gtd\Symfony\Location\Controller;

use Gtd\Application\Location\Query\QuittanceReadModel;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Symfony\Shared\Controller\AbstractApiController;

class QuittanceController extends AbstractApiController
{
    private QuittanceReadModel $quittanceReadModel;

    public function __construct(QuittanceReadModel $quittanceReadModel)
    {
        $this->quittanceReadModel = $quittanceReadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadModel(): ReadModel
    {
        return $this->quittanceReadModel;
    }
}
