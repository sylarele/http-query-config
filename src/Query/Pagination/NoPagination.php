<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Override;
use Sylarele\HttpQueryConfig\Collections\EloquentCollection;
use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * No pagination (all results).
 */
class NoPagination implements QueryPagination
{
    #[Override]
    public function handleQuery(Builder $builder): QueryResult
    {
        return new EloquentCollection($builder->get());
    }
}
