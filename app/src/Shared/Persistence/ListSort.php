<?php

declare(strict_types=1);

namespace Gtd\Shared\Persistence;

use Gtd\Shared\Application\Query\ListQuery;

/**
 * Represent a single applied sort.
 */
final class ListSort
{
    public string $column;
    public string $order = ListQuery::ORDER_ASC;

    public function __construct(string $column, string $order)
    {
        $this->column = $column;
        $this->order = $order;
    }
}
