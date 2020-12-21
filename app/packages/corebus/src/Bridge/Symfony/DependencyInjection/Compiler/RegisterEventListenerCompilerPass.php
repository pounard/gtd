<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Bridge\Symfony\DependencyInjection\Compiler;

use MakinaCorpus\CoreBus\EventBus\EventListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class RegisterEventListenerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $array = [];
        foreach ($container->findTaggedServiceIds('app.handler', true) as $id => $attributes) {
            $definition = $container->getDefinition($id);
            $className = $definition->getClass();

            if (!$reflexion = $container->getReflectionClass($className)) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $className, $id));
            }
            if ($reflexion->implementsInterface(EventListener::class)) {
                // @todo Find another way, for later.
                $definition->setPublic(true);
                $array[$id] = $className;
            }
        }

        $container
            ->getDefinition('corebus.event.listener.locator.container')
            ->setArguments([$array])
        ;
    }
}
