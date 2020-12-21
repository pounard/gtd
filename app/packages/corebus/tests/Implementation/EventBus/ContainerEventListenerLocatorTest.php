<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\EventBus;

use MakinaCorpus\CoreBus\Implementation\EventBus\ContainerEventListenerLocator;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventA;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventB;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventC;
use MakinaCorpus\CoreBus\Tests\Implementation\Mock\MockEventListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

final class ContainerEventListenerLocatorTest extends TestCase
{
    public function testFind(): void
    {
        $container = new Container();
        $container->set('mock_listener', new MockEventListener());

        $locator = new ContainerEventListenerLocator([
            'mock_listener' => MockEventListener::class,
        ]);
        $locator->setContainer($container);

        $eventA = new MockEventA();
        $iterable = $locator->find($eventA);
        self::assertSame(0, $eventA->count);
        foreach ($iterable as $callback) {
            $callback($eventA);
        }
        self::assertSame(2, $eventA->count);

        $eventB = new MockEventB();
        $iterable = $locator->find($eventB);
        self::assertSame(0, $eventB->count);
        foreach ($iterable as $callback) {
            $callback($eventB);
        }
        self::assertSame(1, $eventB->count);

        $eventC = new MockEventC();

        $iterable = $locator->find($eventC);
        self::assertSame(0, $eventC->count);
        foreach ($iterable as $callback) {
            $callback($eventC);
        }
        self::assertSame(0, $eventC->count);
    }
}
