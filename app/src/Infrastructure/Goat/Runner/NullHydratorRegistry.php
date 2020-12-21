<?php

declare (strict_types=1);

namespace Gtd\Infrastructure\Goat\Runner;

use Goat\Runner\Hydrator\HydratorRegistry;

final class NullHydratorRegistry implements HydratorRegistry
{
    /**
     * {@inheritdoc}
     */
    public function getHydrator(string $className): callable
    {
        throw new \Exception("Not implemented yet.");
    }
}
