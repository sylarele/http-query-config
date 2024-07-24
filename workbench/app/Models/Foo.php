<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;
use Workbench\App\Builders\FooBuilder;

/**
 * @property string $name
 * @property int $size
 * @method static FooBuilder query()
 */
class Foo extends Model
{
    /**
     * @param QueryBuilder $query
     */
    #[Override]
    public function newEloquentBuilder($query): FooBuilder
    {
        return new FooBuilder($query);
    }
}
