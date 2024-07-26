<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;
use Workbench\App\Builders\FooBuilder;
use Workbench\App\Enums\FooState;

/**
 * @property string $name
 * @property int $size
 * @method static FooBuilder query()
 */
class Foo extends Model
{
    /** @var array<string, string> */
    protected $casts = [
        'state' => FooState::class,
    ];

    public function bars(): HasMany
    {
        return $this->hasMany(Foo::class);
    }

    /**
     * @param QueryBuilder $query
     */
    #[Override]
    public function newEloquentBuilder($query): FooBuilder
    {
        return new FooBuilder($query);
    }
}
