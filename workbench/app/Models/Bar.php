<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;
use Workbench\App\Builders\BarBuilder;

/**
 * @property int $foo_id
 * @property Foo $foo
 * @method static BarBuilder query()
 */
class Bar extends Model
{
    public function foo(): BelongsTo
    {
        return $this->belongsTo(Foo::class);
    }

    /**
     * @param QueryBuilder $query
     */
    #[Override]
    public function newEloquentBuilder($query): BarBuilder
    {
        return new BarBuilder($query);
    }
}
