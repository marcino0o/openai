<?php

declare(strict_types=1);

namespace RWS\Openai\Exception;

use Throwable;

class LimitExceededException extends OpenAIClientException
{
    public static function fromPrevious(Throwable $previous): self
    {
        return new self('You exceeded your current quota', previous: $previous);
    }
}
