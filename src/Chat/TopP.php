<?php

declare(strict_types=1);

namespace Openai\Chat;

use InvalidArgumentException;

/**
 * Nucleus sampling parameter between 0 and 1. The model considers tokens with top_p probability mass.
 */
readonly class TopP
{
    private const float MIN_VALUE = 0.0;
    private const float MAX_VALUE = 1.0;

    private function __construct(
        public ?float $value
    ) {
    }

    public static function tryFrom(mixed $value): self
    {
        if ($value !== null && (!is_float($value) || $value < self::MIN_VALUE || $value > self::MAX_VALUE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value for top_p, should be null or float number between %s and %s',
                    self::MIN_VALUE,
                    self::MAX_VALUE
                )
            );
        }

        return new self($value);
    }
}
