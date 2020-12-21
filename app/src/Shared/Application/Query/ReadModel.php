<?php

declare(strict_types=1);

namespace Gtd\Shared\Application\Query;

interface ReadModel
{
    public function find($id): ?object;

    public function exists($id): bool;

    public function list(ListQuery $query): ListResponse;
}
