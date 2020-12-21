<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\Mock;

use MakinaCorpus\CoreBus\CommandBus\Command;

final class MockCommandC implements Command
{
    public bool $done = false;
}
