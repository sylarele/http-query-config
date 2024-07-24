<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;
use Workbench\App\Builders\BarBuilder;

/**
 * @method static BarBuilder query()
 */
class Bar extends Model
{
    /**
     * @param  QueryBuilder  $query
     */
    #[Override]
    public function newEloquentBuilder($query): BarBuilder
    {
        return new BarBuilder($query);
    }
}
