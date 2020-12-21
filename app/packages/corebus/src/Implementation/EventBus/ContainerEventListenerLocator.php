<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\EventBus;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;
use MakinaCorpus\CoreBus\EventBus\EventListenerLocator;
use MakinaCorpus\CoreBus\Implementation\Type\CallableReference;
use MakinaCorpus\CoreBus\Implementation\Type\CallableReferenceList;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class ContainerEventListenerLocator implements EventListenerLocator, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private CallableReferenceList $referenceList;

    /**
     * @param array<string,string> $references
     */
    public function __construct(array $references)
    {
        $this->referenceList = new CallableReferenceList(DomainEvent::class, true);

        foreach ($references as $id => $className) {
            $this->referenceList->appendFromClass($className, $id);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function find(DomainEvent $event): iterable
    {
        $references = $this->referenceList->all(\get_class($event));

        foreach ($references as $reference) {
            \assert($reference instanceof CallableReference);

            $service = $this->container->get($reference->serviceId);

            yield static fn (DomainEvent $command) => $service->{$reference->methodName}($command);
        }
    }
}
