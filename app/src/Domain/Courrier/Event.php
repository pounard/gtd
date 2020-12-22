<?php

declare(strict_types=1);

namespace Gtd\Domain\Courrier\Event;

use Gtd\Domain\Courrier\Model\Courrier;
use MakinaCorpus\CoreBus\EventBus\DomainEvent;

final class CourrierAddedEvent implements DomainEvent
{
    public Courrier $courrier;

    public function __construct(Courrier $courrier)
    {
        $this->courrier = $courrier;
    }
}
