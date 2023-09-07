<?php

declare(strict_types=1);

namespace RWS\Openai;

readonly class Configuration
{
    public function __construct(
        public string $apiKey,
        public string $organizationID,
    ) {
    }
}
