<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Collections;

use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

/**
 * Override Laravel's Eloquent Collection to implement our QueryResult interface.
 */
class EloquentCollection extends BaseCollection implements QueryResult
{
}
