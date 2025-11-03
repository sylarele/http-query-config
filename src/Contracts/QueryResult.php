<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use IteratorAggregate;

/**
 * Generic interface used on the different pagination result types.
 *
 * @template TModel of Model
 *
 * @mixin Collection<int,TModel>
 *
 * @extends IteratorAggregate<int,TModel>
 *
 */
interface QueryResult extends IteratorAggregate
{
}
