<?php

declare(strict_types=1);

namespace Openai\Chat;

use Countable;
use Iterator;
use Openai\IteratorTrait;

/**
 * @implements Iterator<Choice>
 * @method Choice|null current()
 */
class Choices implements Iterator, Countable
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

    public function count(): int
    {
        return count($this->items);
    }
}
