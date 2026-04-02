<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Unit;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;
use Sylarele\HttpQueryConfig\Transformers\CarbonTransformer;
use Sylarele\HttpQueryConfig\Transformers\EnumListTransformer;
use Sylarele\HttpQueryConfig\Transformers\EnumTransformer;
use Sylarele\HttpQueryConfig\Transformers\FloatTransformer;
use Sylarele\HttpQueryConfig\Transformers\IntegerTransformer;
use Workbench\App\Enums\FooState;

/**
 * @internal
 */
#[CoversClass(CarbonTransformer::class)]
#[CoversClass(EnumListTransformer::class)]
#[CoversClass(EnumTransformer::class)]
#[CoversClass(FloatTransformer::class)]
#[CoversClass(IntegerTransformer::class)]
final class TransformerTest extends TestCase
{
    public function testCarbonTransform(): void
    {
        $transformer = new CarbonTransformer();

        self::expectNotToPerformAssertions();
        $transformer->transform('01-01-1970');
    }

    public function testCarbonTransformException(): void
    {
        $transformer = new CarbonTransformer();

        self::expectException(Exception::class);
        $transformer->transform('error');
    }

    public function testFloatTransformer(): void
    {
        $transformer = new FloatTransformer();
        $result = $transformer->transform('10.00');

        self::assertSame(10.00, $result);
    }

    public function testFloatTransformerException(): void
    {
        $transformer = new FloatTransformer();

        self::expectException(InvalidTransformerArgumentTypeException::class);
        $transformer->transform('error');
    }

    public function testIntegerTransformer(): void
    {
        $transformer = new IntegerTransformer();
        $result = $transformer->transform('10');

        self::assertSame(10, $result);
    }

    public function testIntegerTransformerException(): void
    {
        $transformer = new IntegerTransformer();

        self::expectException(InvalidTransformerArgumentTypeException::class);
        $transformer->transform('error');
    }

    public function testEnumTransformer(): void
    {
        $transformer = new EnumTransformer(FooState::class);
        $result = $transformer->transform('active');

        self::assertInstanceOf(FooState::class, $result);
        self::assertSame(FooState::Active, $result);
    }

    public function testEnumTransformerException(): void
    {
        $transformer = new EnumTransformer(FooState::class);

        self::expectException(InvalidTransformerArgumentTypeException::class);
        $transformer->transform('error');
    }

    public function testEnumListTransformer(): void
    {
        $transformer = new EnumListTransformer(FooState::class);
        $result = $transformer->transform(['active', 'pending']);

        self::assertSame([FooState::Active, FooState::Pending], $result);
    }

    public function testEnumListTransformerException(): void
    {
        $transformer = new EnumListTransformer(FooState::class);

        self::expectException(InvalidTransformerArgumentTypeException::class);
        $transformer->transform(['error']);
    }
}
