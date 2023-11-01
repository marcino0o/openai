<?php

declare(strict_types=1);

namespace Openai\Chat;

use InvalidArgumentException;

/**
 * Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency
 * in the text so far, decreasing the model's likelihood to repeat the same line verbatim.
 */
readonly class FrequencyPenalty
{
    private const MIN_VALUE = -2;
    private const MAX_VALUE = 2;

    public ?float $value;

    public function __construct(
        ?float $value
    ) {
        $this->assertValid($value);
        $this->value = $value;
    }

    public function from(?float $value): self
    {
        return new self($value);
    }

    private function assertValid(?float $value): void
    {
        if ($value !== null && ($value < self::MIN_VALUE || $value > self::MAX_VALUE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value for frequency penalty, should be null or number between %s and %s',
                    self::MIN_VALUE,
                    self::MAX_VALUE
                )
            );
        }
    }
}
