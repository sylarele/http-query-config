<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Override;
use Workbench\App\Models\Foo;

/**
 * @template TModelClass of Foo
 *
 * @extends Factory<TModelClass>
 */
class FooFactory extends Factory
{
    /** @var class-string<TModelClass> */
    protected $model = Foo::class;

    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
