<?php

declare(strict_types=1);

namespace RWS\Openai\Chat;

use Iterator;
use RWS\Openai\IteratorTrait;

class Messages implements Iterator
{
    use IteratorTrait;

    public function __construct(Message ...$messages)
    {
        $this->items = $messages;
    }
}
