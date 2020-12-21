<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\CommandBus;

use MakinaCorpus\CoreBus\CommandBus\CommandHandlerLocator;
use MakinaCorpus\CoreBus\CommandBus\CommandResponsePromise;
use MakinaCorpus\CoreBus\CommandBus\SynchronousCommandBus;
use MakinaCorpus\CoreBus\Implementation\CommandBus\Response\SynchronousCommandResponsePromise;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class MemoryCommandBus implements SynchronousCommandBus, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CommandHandlerLocator $handlerLocator;

    public function __construct(CommandHandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchCommand($command): CommandResponsePromise
    {
        $this->logger->debug("MemoryCommandBus: Received command: {command}", ['command' => $command]);

        try {
            return SynchronousCommandResponsePromise::success(
                ($this->handlerLocator->find($command))($command)
            );
        } catch (\Throwable $e) {
            $this->logger->error("MemoryCommandBus: Error while processing: {command}: {exception}", ['command' => $command, 'exception' => $e]);

            throw $e;
        }
    }
}
