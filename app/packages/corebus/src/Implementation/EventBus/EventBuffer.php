<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\EventBus;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\Implementation\EventBus\Error\EventBufferAlreadyClosedError;

interface EventBuffer extends \Countable
{
    /**
     * Add domain event to buffer.
     *
     * @throws EventBufferAlreadyClosedError
     */
    public function add(DomainEvent $event): void;

    /**
     * Flush current buffer.
     */
    public function flush(): iterable;

    /**
     * Discard current buffer.
     */
    public function discard(): void;
}
