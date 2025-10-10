<?php

declare(strict_types=1);

namespace Openai\Chat;

readonly class Usage
{
    public function __construct(
        public int $promptTokens, // Number of tokens in the prompt.
        public int $completionTokens, // Number of tokens in the generated completion.
        public int $totalTokens // Total number of tokens used in the request (prompt + completion).
    ) {
    }

    /**
     * @param array{prompt_tokens: integer, completion_tokens: integer, total_tokens: integer} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            promptTokens: $data['prompt_tokens'],
            completionTokens: $data['completion_tokens'],
            totalTokens: $data['total_tokens']
        );
    }
}
