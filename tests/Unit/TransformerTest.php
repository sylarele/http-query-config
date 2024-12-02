<?php

namespace Tests\Sylarele\HttpQueryConfig\Unit\Transformers;


use Exception;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;
use Sylarele\HttpQueryConfig\Transformers\CarbonTransformer;
use Sylarele\HttpQueryConfig\Transformers\FloatTransformer;
use Sylarele\HttpQueryConfig\Transformers\IntegerTransformer;

class CarbonTransformerTest extends TestCase
{
    public function testCarbonTransform(): void
    {
        $transformer = new CarbonTransformer();

        self::assertInstanceOf(
            Carbon::class,
            $transformer->transform('01-01-1970')
        );
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

        self::assertIsFloat($result);
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

        self::assertIsInt($result);
        self::assertSame(10, $result);
    }

    public function testIntegerTransformerException(): void
    {
        $transformer = new IntegerTransformer();

        self::expectException(InvalidTransformerArgumentTypeException::class);
        $transformer->transform('error');
    }
}