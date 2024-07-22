<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Collections;

use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Override Laravel's LengthAwarePaginator to implement our QueryResult interface.
 */
class LengthAwarePaginator extends BasePaginator implements QueryResult
{
    public static function empty(): self
    {
        return new self(
            items: [],
            total: 0,
            perPage: 0,
            currentPage: 1,
            options: [],
        );
    }
}
