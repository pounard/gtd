<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\CommandBus;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\EventBus\EventBusAware;
use MakinaCorpus\CoreBus\EventBus\EventBusAwareTrait;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractCommandHandler implements CommandHandler, CommandBusAware, EventBusAware
{
    use CommandBusAwareTrait, EventBusAwareTrait;

    protected function notifyEvent(DomainEvent $event): void
    {
        $this->getEventBus()->notifyEvent($event);
    }
}
