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

    /**
     * @param array{index: integer, message: array{content: string}, finish_reason: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            index: $data['index'],
            message: Message::fromAssistant($data['message']['content']),
            finishReason: FinishReason::from($data['finish_reason'])
        );
    }
}
