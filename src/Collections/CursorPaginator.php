<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Collections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\CursorPaginator as BasePaginator;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Override Laravel's CursorPaginator to implement our QueryResult interface.
 *
 * @template TModel of Model
 *
 * @implements QueryResult<TModel>
 */
class CursorPaginator extends BasePaginator implements QueryResult
{
}
