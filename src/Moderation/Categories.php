<?php

declare(strict_types=1);

namespace Openai\Moderation;

use Iterator;
use Openai\IteratorTrait;

/**
 * @implements Iterator<Category>
 */
class Categories implements Iterator
{
    use IteratorTrait;

    public function __construct(Category ...$messages)
    {
        $this->items = $messages;
    }
}
