<?php

declare(strict_types=1);

namespace Gtd\Symfony\Location\Controller;

use Gtd\Application\Location\Query\PersonneReadModel;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Symfony\Shared\Controller\AbstractApiController;

class PersonneApiController extends AbstractApiController
{
    private PersonneReadModel $personneReadModel;

    public function __construct(PersonneReadModel $personneReadModel)
    {
        $this->personneReadModel = $personneReadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadModel(): ReadModel
    {
        return $this->personneReadModel;
    }
}
