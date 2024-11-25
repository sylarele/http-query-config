<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

use Illuminate\Support\Collection;
use IteratorAggregate;

/**
 * Generic interface used on the different pagination result types.
 *
 * @template TModel
 *
 * @mixin Collection<int,TModel>
 *
 * @extends IteratorAggregate<int,TModel>
 *
 */
interface QueryResult extends IteratorAggregate
{
}
