<?php

declare(strict_types=1);

namespace Openai\Image;

use DateTimeImmutable;
use Openai\Utils\DateTimeUtils;
use Openai\Utils\JsonUtils;

final readonly class ImageResponse
{
    /** @var Image[]  */
    public array $images;

    public function __construct(
        public DateTimeImmutable $created,
        Image ...$images
    ) {
        $this->images = $images;
    }
    public static function fromJson(string $json): self
    {
        $data = JsonUtils::decode($json);

        return new self(
            DateTimeUtils::fromTimestamp($data['created']),
            ...array_map(
                static fn ($image): Image => new Image(url: $image['url'] ?? null, base64: $image['b64_json'] ?? null),
                $data['data']
            )
        );
    }
}
