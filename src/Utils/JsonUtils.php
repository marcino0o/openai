<?php

declare(strict_types=1);

namespace Openai\Utils;

use InvalidArgumentException;
use Throwable;

final readonly class JsonUtils
{
    /**
     * @throws InvalidArgumentException
     */
    public static function decode(// @phpstan-ignore missingType.iterableValue
        string $json,
        bool $assoc = true,
        int $depth = 512,
        int $options = JSON_THROW_ON_ERROR
    ): array {
        try {
            $data = json_decode($json, $assoc, $depth, $options); // @phpstan-ignore argument.type
        } catch (Throwable) {
            throw new InvalidArgumentException('Provided string must be a valid json');
        }

        return $data; // @phpstan-ignore return.type
    }
}
