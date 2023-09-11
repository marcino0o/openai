<?php

declare(strict_types=1);

namespace Openai\Chat;

use Iterator;
use Openai\IteratorTrait;

/**
 * @implements Iterator<Message>
 */
class Messages implements Iterator
{
    use IteratorTrait;

    public function __construct(Message ...$messages)
    {
        $this->items = $messages;
    }
}
