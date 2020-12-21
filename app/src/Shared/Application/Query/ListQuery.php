<?php

declare(strict_types=1);

namespace Gtd\Shared\Application\Query;

/**
 * This interface is compatible with front library foodget DataQuery
 * interface and is meant to be serialized as-is during API calls for it
 * to be send easily from the front side.
 */
final class ListQuery
{
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';
    const LIMIT_DEFAULT = 100;

    public int $page = 1;
    public int $limit = self::LIMIT_DEFAULT;
    public ?string $sortColumn = null;
    public string $sortOrder = self::ORDER_ASC;
    /** @var array<string,string|string[]> */
    public array $query = [];

    public function __construct(
        array $query = [],
        ?string $sortColumn = null,
        string $sortOrder = self::ORDER_ASC,
        int $page = 1,
        int $limit = self::LIMIT_DEFAULT
    ) {
        $this->limit = $limit;
        $this->page = $page;
        $this->query = $query;
        $this->sort = $sortColumn;
        $this->sortOrder = $sortOrder;
    }

    public function computeOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
