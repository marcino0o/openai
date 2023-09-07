<?php

declare(strict_types=1);

namespace RWS\Openai\Moderation;

use Iterator;
use RWS\Openai\IteratorTrait;

class Categories implements Iterator
{
    use IteratorTrait;

    public function __construct(Category ...$messages)
    {
        $this->items = $messages;
    }
}
