<?php

declare(strict_types=1);

namespace Openai\Image;

use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

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
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new InvalidArgumentException('Provided string must be a valid json');
        }

        return new self(
            (new DateTimeImmutable())->setTimestamp($data['created']),
            ...array_map(
                static fn ($image): Image => new Image(url: $image['url'] ?? null, base64: $image['b64_json'] ?? null),
                $data['data']
            )
        );
    }
}
