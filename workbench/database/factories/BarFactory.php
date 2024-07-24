<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Override;
use Workbench\App\Models\Bar;

/**
 * @template TModelClass of Bar
 *
 * @extends Factory<TModelClass>
 */
class BarFactory extends Factory
{
    /** @var class-string<TModelClass> */
    protected $model = Bar::class;

    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
