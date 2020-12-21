<?php

declare(strict_types=1);

namespace Gtd\Symfony\Location\Controller;

use Gtd\Application\Location\Query\LogementReadModel;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Symfony\Shared\Controller\AbstractApiController;

class LogementApiController extends AbstractApiController
{
    private LogementReadModel $logementReadModel;

    public function __construct(LogementReadModel $logementReadModel)
    {
        $this->logementReadModel = $logementReadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadModel(): ReadModel
    {
        return $this->logementReadModel;
    }
}
