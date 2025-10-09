<?php

declare(strict_types=1);

namespace Openai\Audio;

use Openai\Utils\JsonUtils;

final readonly class TranscriptionResponse
{
    public function __construct(public string $text) {
    }

    public static function fromJson(string $json): self
    {
        $data = JsonUtils::decode($json);

        return new self($data['text']);
    }
}
