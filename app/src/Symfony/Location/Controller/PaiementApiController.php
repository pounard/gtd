<?php

declare(strict_types=1);

namespace Gtd\Symfony\Location\Controller;

use Gtd\Application\Location\Query\PaiementReadModel;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Symfony\Shared\Controller\AbstractApiController;

class PaiementApiController extends AbstractApiController
{
    private PaiementReadModel $paiementReadModel;

    public function __construct(PaiementReadModel $paiementReadModel)
    {
        $this->paiementReadModel = $paiementReadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadModel(): ReadModel
    {
        return $this->paiementReadModel;
    }
}
