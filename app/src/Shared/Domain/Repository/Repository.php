<?php

declare(strict_types=1);

namespace Gtd\Shared\Domain\Repository;

interface Repository
{
    public function find($id): ?object;

    public function exists($id): bool;
}
