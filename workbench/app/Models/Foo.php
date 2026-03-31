<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Override;
use Workbench\App\Builders\FooBuilder;
use Workbench\App\Enums\FooState;

/**
 * - Attributes.
 * @property int $id
 * @property string $name
 * @property int $size
 * @property FooState $state
 *  - Relations
 * @property Collection<int,Bar> $bars
 *  - Support
 * @method static FooBuilder query()
 */
class Foo extends Model
{
    /**
     * @return HasMany<Bar, $this>
     */
    public function bars(): HasMany
    {
        return $this->hasMany(Bar::class);
    }

    /**
     * @param QueryBuilder $query
     */
    #[Override]
    public function newEloquentBuilder($query): FooBuilder
    {
        return new FooBuilder($query);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'state' => FooState::class,
        ];
    }
}
