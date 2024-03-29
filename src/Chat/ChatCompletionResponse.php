<?php

declare(strict_types=1);

namespace Openai\Chat;

use DateTimeImmutable;
use InvalidArgumentException;
use Openai\Model;
use Throwable;

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
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new InvalidArgumentException('Provided string must be a valid json');
        }

        return new self(
            id: $data['id'],
            choices: Choices::fromArray($data['choices']),
            created: (new DateTimeImmutable())->setTimestamp($data['created']),
            model: Model::tryFrom($data['model']),
            usage: Usage::fromArray($data['usage'])
        );
    }
}
