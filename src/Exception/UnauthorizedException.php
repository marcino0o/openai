<?php

declare(strict_types=1);

namespace Openai\Exception;

use Throwable;

class UnauthorizedException extends OpenAIClientException
{
    public static function fromPrevious(Throwable $previous): self
    {
        return new self('Incorrect API key provided', previous: $previous);
    }
}
