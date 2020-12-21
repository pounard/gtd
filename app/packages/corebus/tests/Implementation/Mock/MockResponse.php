<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Tests\Implementation\Mock;

use MakinaCorpus\CoreBus\CommandBus\Command;

final class MockResponse
{
    public Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }
}
