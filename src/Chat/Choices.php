<?php

declare(strict_types=1);

namespace RWS\Openai\Chat;

use Iterator;
use RWS\Openai\IteratorTrait;

class Choices implements Iterator
{
    use IteratorTrait;

    public function __construct(Choice ...$choices)
    {
        $this->items = $choices;
    }

    public static function fromArray(array $data): self
    {
        return new self(...array_map(static fn (array $choice): Choice => Choice::fromArray($choice), $data));
    }
}
