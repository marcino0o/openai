<?php

declare(strict_types=1);

namespace Openai;

use Openai\Utils\JsonUtils;

abstract class OpenaiResponse
{
    public function fromJson(string $json): static
    {
        return $this->fromArray(JsonUtils::decode($json));
    }

    abstract protected function fromArray(mixed $data): static;
}
