<?php

declare(strict_types=1);

namespace Openai\Tests;

use InvalidArgumentException;
use Openai\Chat\ChatTemperature;
use PHPUnit\Framework\TestCase;

class TemperatureTest extends TestCase
{
    /**
     * @test
     * @dataProvider validTemperatureValues
     */
    public function shouldCreateTemperature(?float $validValue): void
    {
        $sut = ChatTemperature::tryFrom($validValue);
        self::assertEquals($validValue, $sut->value);
    }

    public static function validTemperatureValues(): array
    {
        return [
            [null],
            [0],
            [0.9],
            [1.2],
            [1.9],
            [2],
        ];
    }

    /**
     * @test
     * @dataProvider invalidTemperatureValues
     */
    public function shouldNotCreateTemperature(mixed $invalidValue): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChatTemperature::tryFrom($invalidValue);
    }

    public static function invalidTemperatureValues(): array
    {
        return [
            ['null'],
            [-1],
            ['1'],
            ['a'],
            [true],
            [false],
            [2.1],
        ];
    }
}
