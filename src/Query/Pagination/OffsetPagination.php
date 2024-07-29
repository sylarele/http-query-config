<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Override;
use Sylarele\HttpQueryConfig\Collections\LengthAwarePaginator;
use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Offset pagination (standard pagination).
 */
readonly class OffsetPagination implements QueryPagination
{
    public function __construct(
        protected int $page,
        protected int $limit,
    ) {
    }

    /**
     * @return int the page number
     */
    public function getPage(): int
    {
        return $this->page;
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
        $result = $builder->paginate(
            perPage: $this->limit,
            page: $this->page,
        );

        return new LengthAwarePaginator(
            items: $result->items(),
            total: $result->total(),
            perPage: $result->perPage(),
            currentPage: $result->currentPage(),
        );
    }
}
