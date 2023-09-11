<?php

declare(strict_types=1);

namespace Openai\Image;

readonly class Image
{
    public function __construct(
        public ?string $url = null,
        public ?string $base64 = null,
    ) {
    }
}
