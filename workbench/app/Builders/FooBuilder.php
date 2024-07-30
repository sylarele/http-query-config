<?php

declare(strict_types=1);

namespace Workbench\App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Workbench\App\Enums\FooState;
use Workbench\App\Models\Foo;

/**
 * @template TModelClass of Foo
 *
 * @extends Builder<TModelClass>
 */
class FooBuilder extends Builder
{
    use HttpBuilder;

    public function whereState(FooState $state): self
    {
        return $this->where('state', '=', $state);
    }

    /**
     * @param array<int,FooState> $states
     */
    public function whereStates(array $states): self
    {
        $this->whereIn('state', $states);

        return $this;
    }
}
