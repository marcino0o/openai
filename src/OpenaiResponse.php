<?php

declare(strict_types=1);

namespace Openai;

use InvalidArgumentException;
use Throwable;

abstract class OpenaiResponse
{
    public function fromJson(string $json): static
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new InvalidArgumentException('Provided string must be a valid json');
        }

        return $this->fromArray($data);
    }

    abstract protected function fromArray(mixed $data): static;
}
