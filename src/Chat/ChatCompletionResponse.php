<?php

declare(strict_types=1);

namespace Openai\Chat;

use DateTimeImmutable;
use Openai\Model;
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
        $data = JsonUtils::decode($json);

        return new self(
            id: $data['id'],
            choices: Choices::fromArray($data['choices']),
            created: (new DateTimeImmutable())->setTimestamp($data['created']),
            model: Model::tryFrom($data['model']),
            usage: Usage::fromArray($data['usage'])
        );
    }
}
