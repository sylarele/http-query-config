<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Override;
use Sylarele\HttpQueryConfig\Collections\CursorPaginator;
use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Cursor pagination (better for infinite scrolling).
 */
class CursorPagination implements QueryPagination
{
    public function __construct(
        protected readonly ?string $cursor,
        protected readonly int $limit,
    ) {
    }

    /**
     * @return string|null the cursor identifier
     */
    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    /**
     * @return int the query limit (items per page)
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    #[Override]
    public function handleQuery(Builder $builder): QueryResult
    {
        $result = $builder->cursorPaginate(
            perPage: $this->limit,
            cursor: $this->cursor,
        );

        /** @var \Illuminate\Support\Collection<int,Model> $items */
        $items = $result->items();

        return new CursorPaginator(
            items: new Collection($items),
            perPage: $result->perPage(),
            cursor: $result->cursor(),
        );
    }
}
