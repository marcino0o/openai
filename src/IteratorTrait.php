<?php

declare(strict_types=1);

namespace RWS\Openai;

trait IteratorTrait
{
    private array $items;
    private int $pointer = 0;

    public function map(callable $fn): array
    {
        $result = [];

        foreach ($this as $item) {
            $result[] = $fn( $item);
        }

        return $result;
    }

    public function current(): mixed
    {
        return $this->items[$this->pointer];
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        return $this->pointer < count($this->items);
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }
}
