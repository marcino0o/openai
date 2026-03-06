<?php

declare(strict_types=1);

namespace Openai\Chat;

use InvalidArgumentException;

/**
 * Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they
 * appear in the text so far, increasing the model's likelihood to talk about new topics.
 */
readonly class PresencePenalty
{
    private const float MIN_VALUE = -2.0;
    private const float MAX_VALUE = 2.0;

    private function __construct(
        public ?float $value
    ) {
    }

    public static function tryFrom(mixed $value): self
    {
        if ($value !== null && (!is_float($value) || $value < self::MIN_VALUE || $value > self::MAX_VALUE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value for presence penalty, should be null or float number between %s and %s',
                    self::MIN_VALUE,
                    self::MAX_VALUE
                )
            );
        }

        return new self($value);
    }
}
