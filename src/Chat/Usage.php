<?php

declare(strict_types=1);

namespace RWS\Openai\Chat;

readonly class Usage
{
    public function __construct(
        public int $promptTokens, // Number of tokens in the prompt.
        public int $completionTokens, // Number of tokens in the generated completion.
        public int $totalTokens // Total number of tokens used in the request (prompt + completion).
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            promptTokens: $data['prompt_tokens'],
            completionTokens: $data['completion_tokens'],
            totalTokens: $data['total_tokens']
        );
    }
}
