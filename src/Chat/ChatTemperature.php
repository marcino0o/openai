<?php

declare(strict_types=1);

namespace Openai\Chat;

use InvalidArgumentException;

/**
 * What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random,
 * while lower values like 0.2 will make it more focused and deterministic.
 */
readonly class ChatTemperature
{
    private const MIN_VALUE = 0;
    private const MAX_VALUE = 2.0;

    private function __construct(
        public ?float $value
    ) {
    }

    public static function tryFrom(mixed $value): self
    {
        if ($value !== null && (!is_float($value) || $value < self::MIN_VALUE || $value > self::MAX_VALUE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value for temperature, should be null or float number between %s and %s',
                    self::MIN_VALUE,
                    self::MAX_VALUE
                )
            );
        }

        return new self($value);
    }
}
