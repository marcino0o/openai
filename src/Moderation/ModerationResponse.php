<?php

declare(strict_types=1);

namespace Openai\Moderation;

use InvalidArgumentException;
use Openai\Model;
use Throwable;

readonly class ModerationResponse
{
    public function __construct(
        public string $id,
        public Model $model,
        public ModerationResult $moderationResult
    ) {
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
            model: Model::tryFrom($data['model']),
            moderationResult: ModerationResult::fromArray($data['results'][0])
        );
    }
}
