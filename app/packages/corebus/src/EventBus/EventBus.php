<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\EventBus;

interface EventBus
{
    public function notifyEvent(DomainEvent $event): void;
}
