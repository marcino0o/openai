<?php

declare(strict_types=1);

namespace RWS\Openai\Audio;

use InvalidArgumentException;
use Throwable;

readonly class TranscriptionResponse
{
    public function __construct(public string $text) {
    }

    public static function fromJson(string $json): self
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new InvalidArgumentException('Provided string must be a valid json');
        }

        return new self($data['text']);
    }
}
