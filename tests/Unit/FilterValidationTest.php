<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Query\Filter;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\App\Builders\FooBuilder;
use Workbench\App\Models\Foo;

/**
 * @internal
 */
#[CoversClass(Filter::class)]
final class FilterValidationTest extends TestCase
{
    public function testShouldUseTheTypeValidationByDefault(): void
    {
        $filter = $this->makeFilter();

        self::assertSame(
            ['nullable', 'string', 'max:256'],
            $filter->getValidation()['value']
        );
    }

    public function testShouldReplaceTheTypeValidation(): void
    {
        $filter = $this->makeFilter()
            ->withValidation(['required', 'string', 'max:10']);

        $validation = $filter->getValidation();

        self::assertSame(['required', 'string', 'max:10'], $validation['value']);
        self::assertArrayHasKey('not', $validation);
        self::assertArrayHasKey('mode', $validation);
    }

    public function testShouldAddValidationOnASubKeyOfTheValue(): void
    {
        $filter = $this->makeFilter()
            ->type(FilterType::Array)
            ->addedValidation('*', ['required', 'string']);

        $validation = $filter->getValidation();

        self::assertSame(['nullable', 'array'], $validation['value']);
        self::assertSame(['required', 'string'], $validation['value.*']);
    }

    /**
     * @return Filter<Foo,FooBuilder>
     */
    private function makeFilter(): Filter
    {
        /** @var Filter<Foo,FooBuilder> $filter */
        $filter = new Filter(
            model: new Foo(),
            name: 'state',
            mutate: static fn (): null => null,
        );

        return $filter->type(FilterType::String);
    }
}
