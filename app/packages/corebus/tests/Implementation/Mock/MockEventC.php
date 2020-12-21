<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\Mock;

use MakinaCorpus\CoreBus\EventBus\DomainEvent;

final class MockEventC implements DomainEvent
{
    public int $count = 0;
}
