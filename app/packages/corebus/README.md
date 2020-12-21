# CoreBus - Command and event buses interface and logic

This package serves the purpose of sharing interfaces and internal logic
for hexagonal architecture, domain-driven set of projects, command and event
bus.

Let's be honest, the fact is, using this library breaks the oignon architecture
since you'll be dependent upon an external package. Fact is, we do need a way
to share this without copy/pasting it in every project, so here we go, breaking
out the hexagone.

That'll be the only exception, I promise.

# Design

## Basic design

Expected runtime flow is the following:

 - Commands may be dispatched to trigger writes in the system,
 - Commands are always asynchronously handled (you may have a return
   response under certain circumstances),
 - One command implies one transaction on your database backend,
 - During a single command processing, the domain code may raise one or many
   domain events,
 - Domain events are always dispatched synchronously within your domain
   code, within the triggering command transaction.

During the full command processing, the database transaction will be
isolated if the backend permits it. Commit is all or nothing, including
events being emitted during the process.

## Transaction and event buffer

Transaction handling will be completely hidden in the implementations,
your business code will never see it, here is how it works:

 - Domain events while emitted and dispatched internally are stored along
   the way into a volatile temporary buffer,
 - Once command is comsumed and task has ended, transaction will commit,
 - In case of success, buffer is flushed and events may be sent to a bus
   for external application to listen to,
 - In case of failure, transaction rollbacks, event buffer is emptied,
   events are dropped into void.

## Optional event store

If required for your project, you may plug an event store on the event
dispatcher. Two options are possible:

 - Plug in into the internal event dispatcher, events will be stored along
   the way, this requires that the event store works on the same database
   transaction, hence connection, than your domain repositories.
 - Plug in into the event buffer output, which means events will be stored
   after commit, there is no consistency issues anymore, but if event storage
   procedure fails, you will loose history.

## Implementations

Two implementations are provided:

 - In-memory bus, along with null transaction handling (no transaction at all)
   ideal for prototyping and unit-testing,
 - PostgreSQL bus implementation using `makinacorpus/goat-query`, transaction
   handling using the same database connection, reliable and guaranteing data
   consistency.

Everything is hidden behind interfaces and different implementations are easy
to implement. Your projects are not required to choose either one of those
implementations, in the opposite, is encouraged implementing its own.

# Setup

## Standalone

There is no standalone setup guide for now. Refer to provided Symfony
configuration for a concrete example.

## Using Symfony

As of today, this package does not provide Symfony extension, bundle or
auto-configuration, but rather simple example configuration files for your
services and a couple of compiler pass to apply for automatic command handler
and event listener registration.

Please read carefuly one of the files in `samples/symfony/services-*.yaml`,
copy paste its contents into your own `config/services.yaml` and adapt to your
needs.

Then add into your `Kernel.php` file:

```php

use MakinaCorpus\CoreBus\Implementation\Symfony\DependencyInjection\Compiler\RegisterCommandHandlerCompilerPass;
use MakinaCorpus\CoreBus\Implementation\Symfony\DependencyInjection\Compiler\RegisterEventListenerCompilerPass;
use MakinaCorpus\CoreBus\CommandBus\CommandBus;
use MakinaCorpus\CoreBus\CommandBus\CommandBusAware;
use MakinaCorpus\CoreBus\EventBus\EventBus;
use MakinaCorpus\CoreBus\EventBus\EventBusAware;
// ... Your other use statements.

class Kernel extends BaseKernel
{
    // ... Your kernel code.

    /**
     * {@inheritdoc}
     */
    protected function build(ContainerBuilder $container)
    {
        $container
            ->registerForAutoconfiguration(EventBusAware::class)
            ->addMethodCall('setEventBus', [new Reference(EventBus::class)])
        ;

        $container
            ->registerForAutoconfiguration(CommandBusAware::class)
            ->addMethodCall('setCommandBus', [new Reference(CommandBus::class)])
        ;

        $container->addCompilerPass(new RegisterCommandHandlerCompilerPass());
        $container->addCompilerPass(new RegisterEventListenerCompilerPass());
    }
```

This way, this bundle remains very flexible regarding Symfony version, and
leave all complex configuration to you, allowing you much more flexibility in
your application design.

# Usage

Write a simple command:

```php

declare(strict_types=1);

use MakinaCorpus\CoreBus\CommandBus\Command;

final class SayHelloCommand implements Command
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

Write some events:

```php

declare(strict_types=1);

use MakinaCorpus\CoreBus\EventBus\DomainEvent;

final class HelloWasSaidEvent implements DomainEvent
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

Tie a single command handler:

```php

declare(strict_types=1);

use MakinaCorpus\CoreBus\CommandBus\AbstractHandler;

final class SayHello extends AbstractHandler
{
    /*
     * Method name is yours, you may have more than one handler in the
     * same class, do you as wish. Only important thing is to implement
     * the Handler interface (here via the AbstractHandler class).
     */
    public function do(SayHelloCommand $command)
    {
        echo "Hello, ", $command->name, "\n";

        $this->notifyEvent(new HelloWasSaidEvent($command->name));
    }
}
```

You may also write as many event listeners as you wish, then even
may emit events themselves:

```php

declare(strict_types=1);

use MakinaCorpus\CoreBus\EventBus\EventListener;

final class SayHello implements EventListener
{
    /*
     * Method name is yours, you may have more than one handler in the
     * same class, do you as wish. Only important thing is to implement
     * the EventListener interface.
     */
    public function on(HelloWasSaidEvent $event)
    {
        $this->logger->debug("Hello was said to {name}.", ['name' => $event->name]);
    }
}
```

If you correctly plug the Symfony container machinery, glue will be
completely transparent as long as you implement the correct interfaces.

# Overriding implementations

Any interface in this package is a service in the dependency injection container
you will use. You may replace or decorate any of them.
