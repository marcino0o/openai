<?php

declare(strict_types=1);

namespace Openai\Exception;

use Throwable;

class LimitExceededException extends OpenAIClientException
{
    #[\Override]
    public static function fromPrevious(Throwable $previous): self
    {
        return new self('You exceeded your current quota', previous: $previous);
    }
}
