<?php

declare(strict_types=1);

namespace Gtd\Symfony\Location\Controller;

use Gtd\Application\Location\Query\ContratReadModel;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Symfony\Shared\Controller\AbstractApiController;

class ContratApiController extends AbstractApiController
{
    private ContratReadModel $contratReadModel;

    public function __construct(ContratReadModel $contratReadModel)
    {
        $this->contratReadModel = $contratReadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadModel(): ReadModel
    {
        return $this->contratReadModel;
    }
}
