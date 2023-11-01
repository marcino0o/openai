<?php

declare(strict_types=1);

namespace Openai;

use InvalidArgumentException;

readonly class Prompt
{
    private const MAX_PROMPT_LENGTH = 1000;

    private function __construct(public string $text) {
    }

    public static function fromString(string $text): self
    {
        $promptLength = mb_strlen($text);
        if ($promptLength === 0 || $promptLength > self::MAX_PROMPT_LENGTH) {
            throw new InvalidArgumentException('Prompt value should be between 1-1000 characters');
        }

        return new self($text);
    }

    public function __toString(): string
    {
        return $this->text;
    }
}
