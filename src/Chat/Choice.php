<?php

declare(strict_types=1);

namespace Openai\Chat;

readonly class Choice
{
    public function __construct(
        public int $index,
        public Message $message,
        public FinishReason $finishReason,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            index: $data['index'],
            message: Message::fromAssistant($data['message']['content']),
            finishReason: FinishReason::tryFrom($data['finish_reason'])
        );
    }
}
