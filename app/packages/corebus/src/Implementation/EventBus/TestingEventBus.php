<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\EventBus;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\EventBus\EventBus;
use Psr\Log\NullLogger;

final class TestingEventBus implements EventBus
{
    /** @var DomainEvent[] */
    private array $events = [];

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function notifyEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function reset(): void
    {
        $this->events = [];
    }

    /** @return DomainEvent[] */
    public function all(): array
    {
        return $this->events;
    }

    public function count(): int
    {
        return \count($this->events);
    }

    public function countWithClass(string $className): int
    {
        $count = 0;

        foreach ($this->events as $event) {
            if (\get_class($event) === $className) {
                ++$count;
            }
        }

        return $count;
    }

    public function countInstanceOf(string $className): int
    {
        $count = 0;

        foreach ($this->events as $event) {
            if ($event instanceof $className) {
                ++$count;
            }
        }

        return $count;
    }

    public function getAt(int $index)
    {
        if (!isset($this->events[$index])) {
            throw new \InvalidArgumentException(\sprintf("There is no event at index %d", $index));
        }

        return $this->events[$index];
    }

    public function first()
    {
        return $this->getAt(0);
    }

    public function firstWithClass(string $className): int
    {
        foreach ($this->events as $event) {
            if (\get_class($event) === $className) {
                return $event;
            }
        }

        throw new \InvalidArgumentException(\sprintf("There is no event with class %s", $className));
    }

    public function firstInstanceOf(string $className): int
    {
        foreach ($this->events as $event) {
            if ($event instanceof $className) {
                return $event;
            }
        }

        throw new \InvalidArgumentException(\sprintf("There is no event instance of %s", $className));
    }

    public function last()
    {
        return $this->getAt(\count($this->events) - 1);
    }
}
