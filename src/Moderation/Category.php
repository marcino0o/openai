<?php

declare(strict_types=1);

namespace Openai\Moderation;

readonly class Category
{
    public function __construct(
        public string $name,
        public bool $flagged,
        public float $score,
    ) {
    }
}
