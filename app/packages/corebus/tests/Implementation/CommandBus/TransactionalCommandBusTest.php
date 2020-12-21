<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\CommandBus;

use MakinaCorpus\CoreBus\CommandBus\CommandBus;
use MakinaCorpus\CoreBus\CommandBus\CommandResponsePromise;
use MakinaCorpus\CoreBus\CommandBus\Error\CommandHandlerNotFoundError;
use MakinaCorpus\CoreBus\EventBus\EventBus;
use MakinaCorpus\CoreBus\EventBus\EventBusAwareTrait;
use MakinaCorpus\CoreBus\Implementation\CommandBus\TransactionalCommandBus;
use MakinaCorpus\CoreBus\Implementation\CommandBus\Response\SynchronousCommandResponsePromise;
use MakinaCorpus\CoreBus\Implementation\EventBus\ArrayEventBufferManager;
use MakinaCorpus\CoreBus\Implementation\Transaction\NullTransactionManager;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockCommandA;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockCommandB;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockCommandC;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventA;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventB;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventBus;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventC;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockResponse;
use PHPUnit\Framework\TestCase;

final class TransactionalCommandBusTest extends TestCase
{
    public function testCommitFlushEventBuffer(): void
    {
        $internalCommandBus = new TransactionalCommandBusTestCommandBus();
        $externalEventBus = new MockEventBus();
        $internalEventBus = new MockEventBus();

        $commandBus = $this->createCommandBus(
            $internalCommandBus,
            $internalEventBus,
            $externalEventBus
        );
        $internalCommandBus->setEventBus($commandBus);

        $commandBus->dispatchCommand(new MockCommandA());

        self::assertCount(1, $externalEventBus->events);
        self::assertCount(1, $internalEventBus->events);

        $externalEventBus->events = [];
        $internalEventBus->events = [];

        $commandBus->dispatchCommand(new MockCommandC());

        self::assertCount(3, $externalEventBus->events);
        self::assertCount(3, $internalEventBus->events);
    }

    public function testRollbackDiscardEventBuffer(): void
    {
        $internalCommandBus = new TransactionalCommandBusTestCommandBus();
        $externalEventBus = new MockEventBus();
        $internalEventBus = new MockEventBus();

        $commandBus = $this->createCommandBus(
            $internalCommandBus,
            $internalEventBus,
            $externalEventBus
        );
        $internalCommandBus->setEventBus($commandBus);

        try {
            $commandBus->dispatchCommand(new MockCommandB());
        } catch (\DomainException $e) {
        }

        self::assertCount(2, $internalEventBus->events);
        self::assertCount(0, $externalEventBus->events);
    }

    private function createCommandBus(
        CommandBus $commandBus,
        EventBus $internalEventBus,
        EventBus $externalEventBus
    ): TransactionalCommandBus{
        return new TransactionalCommandBus(
            $commandBus,
            $internalEventBus,
            $externalEventBus,
            new ArrayEventBufferManager(),
            new NullTransactionManager()
        );
    }
}

/**
 * @internal
 */
final class TransactionalCommandBusTestCommandBus implements CommandBus
{
    use EventBusAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function dispatchCommand($command): CommandResponsePromise
    {
        if ($command instanceof MockCommandA) {
            $this->getEventBus()->notifyEvent(new MockEventA());

            return SynchronousCommandResponsePromise::success(new MockResponse($command));
        }

        if ($command instanceof MockCommandB) {
            $this->getEventBus()->notifyEvent(new MockEventA());
            $this->getEventBus()->notifyEvent(new MockEventB());

            throw new \DomainException("This should rollback.");
        }

        if ($command instanceof MockCommandC) {
            $this->getEventBus()->notifyEvent(new MockEventA());
            $this->getEventBus()->notifyEvent(new MockEventB());
            $this->getEventBus()->notifyEvent(new MockEventC());

            return SynchronousCommandResponsePromise::success(new MockResponse($command));
        }

        throw CommandHandlerNotFoundError::fromCommand($command);
    }
}
