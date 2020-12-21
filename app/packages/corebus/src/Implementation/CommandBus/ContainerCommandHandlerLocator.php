<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\CommandBus;

use MakinaCorpus\CoreBus\Implementation\Type\CallableReferenceList;
use MakinaCorpus\CoreBus\CommandBus\Command;
use MakinaCorpus\CoreBus\CommandBus\CommandHandlerLocator;
use MakinaCorpus\CoreBus\CommandBus\Error\CommandHandlerNotFoundError;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class ContainerCommandHandlerLocator implements CommandHandlerLocator, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private CallableReferenceList $referenceList;

    /**
     * @param array<string,string> $references
     */
    public function __construct(array $references)
    {
        $this->referenceList = new CallableReferenceList(Command::class, false);

        foreach ($references as $id => $className) {
            $this->referenceList->appendFromClass($className, $id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($command): callable
    {
        $reference = $this->referenceList->first(\get_class($command));

        if (!$reference) {
            throw CommandHandlerNotFoundError::fromCommand($command);
        }

        $service = $this->container->get($reference->serviceId);

        return static fn ($command) => $service->{$reference->methodName}($command);
    }
}
