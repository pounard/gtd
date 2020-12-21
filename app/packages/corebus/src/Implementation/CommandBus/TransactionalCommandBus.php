<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\CommandBus;

use MakinaCorpus\CoreBus\CommandBus\CommandBus;
use MakinaCorpus\CoreBus\CommandBus\CommandResponsePromise;
use MakinaCorpus\CoreBus\CommandBus\SynchronousCommandBus;
use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\EventBus\EventBus;
use MakinaCorpus\CoreBus\Implementation\EventBus\EventBuffer;
use MakinaCorpus\CoreBus\Implementation\EventBus\EventBufferManager;
use MakinaCorpus\CoreBus\Implementation\Transaction\TransactionManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * @todo Handle retry mechanism here.
 */
final class TransactionalCommandBus implements SynchronousCommandBus, EventBus, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CommandBus $commandBus;
    private EventBus $internalEventBus;
    private EventBus $externalEventBus;
    private EventBufferManager $eventBufferManager;
    private TransactionManager $transactionManager;
    private ?EventBuffer $buffer = null;

    public function __construct(
        CommandBus $commandBus,
        EventBus $internalEventBus,
        EventBus $externalEventBus,
        EventBufferManager $eventBufferManager,
        TransactionManager $transactionManager
    ) {
        $this->commandBus = $commandBus;
        $this->internalEventBus = $internalEventBus;
        $this->externalEventBus = $externalEventBus;
        $this->eventBufferManager = $eventBufferManager;
        $this->transactionManager = $transactionManager;
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchCommand($command): CommandResponsePromise
    {
        $transaction = null;
        $this->buffer = $this->eventBufferManager->start();

        try {
            $transaction = $this->transactionManager->start();
            $response = $this->commandBus->dispatchCommand($command);
            $transaction->commit();

            $this->flush();

            return $response;

        } catch (\Throwable $e) {
            if ($transaction) {
                $transaction->rollback();
            }

            $this->discard();

            throw $e;

        } finally {
            if ($this->buffer) {
                $this->buffer->discard();
                $this->buffer = null;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function notifyEvent(DomainEvent $event): void
    {
        if (!$this->buffer) {
            throw new \BadMethodCallException("TransactionalCommandBus: You are not in a transaction, transactional bus cannot send events.");
        }

        $this->internalEventBus->notifyEvent($event);
        $this->buffer->add($event);
    }

    /**
     * Discard all events.
     */
    private function discard(): void
    {
        $this->logger->error("TransactionalCommandBus: Discarded {count} events.", ['count' => \count($this->buffer)]);

        $this->buffer->discard();
        $this->buffer = null;
    }

    /**
     * Send all events.
     */
    private function flush(): void
    {
        $this->logger->debug("TransactionalCommandBus: Will flush {count} events.", ['count' => \count($this->buffer)]);

        $errors = 0;
        $total = 0;

        foreach ($this->buffer->flush() as $event) {
            ++$total;
            try {
                $this->externalEventBus->notifyEvent($event);
            } catch (\Throwable $e) {
                ++$errors;
                $this->logger->error("TransactionalCommandBus: Error while event '{event}' flush.", ['event' => $event]);
            }
        }
        $this->buffer = null;

        $this->logger->debug("TransactionalCommandBus: Flushed {total} events, {error} errors.", ['total' => $total, 'error' => $errors]);
    }
}
