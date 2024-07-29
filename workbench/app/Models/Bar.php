<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;
use Workbench\App\Builders\BarBuilder;

/**
 * - Attributes
 * @property int $id
 * @property string $name
 * @property int $foo_id
 * - Relations
 * @property Foo $foo
 * - Support
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
