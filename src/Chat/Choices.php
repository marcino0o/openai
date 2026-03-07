<?php

declare(strict_types=1);

namespace Openai\Chat;

use Countable;
use Iterator;
use Openai\Utils\IteratorTrait;

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

    /**
     * @param array{index: integer, message: array{content: string}, finish_reason: string}[] $data
     */
    public static function fromArray(array $data): self
    {
        return new self(...array_map(Choice::fromArray(...), $data));
    }

    public function count(): int
    {
        return count($this->items);
    }
}
