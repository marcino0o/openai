<?php

declare(strict_types=1);

namespace Openai\Chat;

use DateTimeImmutable;
use Openai\Model;
use Openai\Utils\DateTimeUtils;
use Openai\Utils\JsonUtils;

final readonly class ChatCompletionResponse
{
    public string $object;

    public function __construct(
        public string $id,
        public Choices $choices,
        public DateTimeImmutable $created,
        public Model $model,
        public Usage $usage,
    ) {
        $this->object = 'chat.completion';
    }

    public static function fromJson(string $json): self
    {
        /** @var array{id: string, choices: array{index: integer, message: array{content: string}, finish_reason: string}[], created: integer, model: string, usage: array{prompt_tokens: integer, completion_tokens: integer, total_tokens: integer}} $data */
        $data = JsonUtils::decode($json);

        return new self(
            id: $data['id'],
            choices: Choices::fromArray($data['choices']),
            created: DateTimeUtils::fromTimestamp($data['created']),
            model: Model::tryFromModelString($data['model']),
            usage: Usage::fromArray($data['usage'])
        );
    }
}
