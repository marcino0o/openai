<?php

declare(strict_types=1);

namespace Openai\Audio;

use InvalidArgumentException;

/**
 * The sampling temperature, between 0 and 1. Higher values like 0.8 will make the output more random, while lower
 * values like 0.2 will make it more focused and deterministic. If set to 0, the model will use log probability to
 * automatically increase the temperature until certain thresholds are hit.
 */
readonly class AudioTemperature
{
    private const MIN_VALUE = 0;
    private const MAX_VALUE = 1.0;

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
