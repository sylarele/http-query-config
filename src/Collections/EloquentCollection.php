<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Collections;

use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Database\Eloquent\Model;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Override Laravel's Eloquent Collection to implement our QueryResult interface.
 *
 * @template TModel of Model
 *
 * @extends BaseCollection<int,TModel>
 *
 * @implements QueryResult<TModel>
 */
class EloquentCollection extends BaseCollection implements QueryResult
{
}
