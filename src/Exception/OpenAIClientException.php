<?php

declare(strict_types=1);

namespace RWS\Openai\Exception;

use Exception;
use Throwable;

class OpenAIClientException extends Exception
{
    public static function fromPrevious(Throwable $previous): self
    {
        return new self(message: 'An OpenAI client exception.', previous: $previous);
    }
}
