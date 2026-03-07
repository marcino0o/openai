<?php

declare(strict_types=1);

namespace Openai\Tests\Unit\Utils;

use InvalidArgumentException;
use Openai\Utils\JsonUtils;
use PHPUnit\Framework\TestCase;

class JsonUtilsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDecodeValidJson(): void
    {
        $decoded = JsonUtils::decode('{"model":"gpt-5","count":2}');

        self::assertSame('gpt-5', $decoded['model']);
        self::assertSame(2, $decoded['count']);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionForInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided string must be a valid json');

        JsonUtils::decode('{"model":"gpt-5"');
    }
}
