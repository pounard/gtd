<?php

declare (strict_types=1);

namespace MakinaCorpus\CoreBus\Bridge\Goat\Dispatcher;

use Goat\Dispatcher\MessageEnvelope;
use Goat\MessageBroker\MessageBroker;
use MakinaCorpus\CoreBus\CommandBus\CommandBus;
use MakinaCorpus\CoreBus\CommandBus\CommandResponsePromise;
use MakinaCorpus\CoreBus\Implementation\CommandBus\Response\NeverCommandResponsePromise;

final class MessageBrokerCommandBus implements CommandBus
{
    private MessageBroker $messageBroker;

    public function __construct(MessageBroker $messageBroker)
    {
        $this->messageBroker = $messageBroker;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchCommand($command): CommandResponsePromise
    {
        $this->messageBroker->dispatch(MessageEnvelope::wrap($command));

        return new NeverCommandResponsePromise();
    }
}
