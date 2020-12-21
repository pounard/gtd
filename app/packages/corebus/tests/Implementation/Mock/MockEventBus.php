<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\Mock;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\EventBus\EventBus;

final class MockEventBus implements EventBus
{
    public array $events = [];

    public function notifyEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
