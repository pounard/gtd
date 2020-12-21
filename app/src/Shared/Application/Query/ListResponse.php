<?php

declare(strict_types=1);

namespace Gtd\Shared\Application\Query;

/**
 * This interface is compatible with front library foodget DataResponse
 * interface and is meant to be serialized as-is during API calls for it
 * to be consumed easily on the front side.
 */
final class ListResponse
{
    public iterable $items;
    public int $count;
    public int $limit;
    public int $page = 1;
    public ?int $total = null;
    public ?string $sortColumn = null;
    public ?string $sortOrder = null;

    public function __construct(
        iterable $items,
        int $count,
        int $limit,
        int $page,
        ?int $total = null,
        ?string $sortColumn = null,
        ?string $sortOrder = null
    ) {
        $this->count = $count;
        $this->items = $items;
        $this->limit = $limit;
        $this->sortColumn = $sortColumn;
        $this->sortOrder = $sortOrder;
        $this->total = $total;
    }
}
