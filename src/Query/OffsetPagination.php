<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Illuminate\Database\Eloquent\Builder;
use Override;
use RuntimeException;
use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Offset pagination (standard pagination).
 */
class OffsetPagination implements QueryPagination
{
    public function __construct(
        protected readonly int $page,
        protected readonly int $limit,
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

        return $result instanceof QueryResult
            ? $result
            : throw new RuntimeException(
                'Missing Laravel binding for LengthAwarePaginator.',
            );
    }
}
