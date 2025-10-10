<?php

declare(strict_types=1);

namespace Openai\Moderation;

use Openai\Model;
use Openai\Utils\JsonUtils;

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
        /** @var array{model: string, results: array{categories: array, category_scores: array, flagged: bool}[]} $data */
        $data = JsonUtils::decode($json);

        return new self(
            id: $data['id'],
            model: Model::tryFromModelString($data['model']),
            moderationResult: ModerationResult::fromArray($data['results'][0])
        );
    }
}
