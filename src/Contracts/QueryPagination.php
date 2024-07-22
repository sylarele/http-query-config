<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface used to mark pagination methods.
 */
interface QueryPagination
{
    /**
     * Applies the pagination to the query and returns the results.
     */
    public function handleQuery(Builder $builder): QueryResult;
}
