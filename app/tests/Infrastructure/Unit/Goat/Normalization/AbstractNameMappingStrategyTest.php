<?php

declare(strict_types=1);

namespace Gtd\Tests\Infrastructure\Unit\Goat\Normalization;

use Gtd\Infrastructure\Goat\Normalization\CommandNameMappingStrategy;
use Gtd\Infrastructure\Goat\Normalization\EventNameMappingStrategy;
use Gtd\Infrastructure\Goat\Normalization\ModelNameMappingStrategy;

/**
 * @todo Test error cases.
 */
final class AbstractNameMappingStrategyTest /* extends TestCase */
{
    public function testCommandNameMappingStrategy(): void
    {
        $strategy = new CommandNameMappingStrategy();

        $className = 'Gtd\Application\Location\Command\ClientAutreAddCommand';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.App.Location.ClientAutreAddCommand',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );

        $className = 'Gtd\Application\Location\Command\Foo\Bar\ClientAutreAddCommand';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.App.Location.Foo.Bar.ClientAutreAddCommand',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );
    }

    public function testEventNameMappingStrategy(): void
    {
        $strategy = new EventNameMappingStrategy();

        $className = 'Gtd\Application\Location\Event\ClientAutreAddedEvent';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.App.Location.ClientAutreAddedEvent',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );

        $className = 'Gtd\Application\Location\Event\Foo\Bar\ClientAutreAddedEvent';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.App.Location.Foo.Bar.ClientAutreAddedEvent',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );
    }

    public function testModelNameMappingStrategy(): void
    {
        $strategy = new ModelNameMappingStrategy();

        $className = 'Gtd\Domain\Location\Model\ClientAutre';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.Dom.Location.ClientAutre',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );

        $className = 'Gtd\Domain\Location\Model\Foo\Bar\ClientAutre';
        $logicalName = $strategy->phpTypeToLogicalName($className);
        self::assertSame(
            'G.Dom.Location.Foo.Bar.ClientAutre',
            $logicalName
        );
        self::assertSame(
            $className,
            $strategy->logicalNameToPhpType($logicalName)
        );
    }
}
