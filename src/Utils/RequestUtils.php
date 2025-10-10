<?php

declare(strict_types=1);

namespace Openai\Utils;

final readonly class RequestUtils
{
    /**
     * @return array{name: string, contents: mixed}
     */
    public static function buildRequestPart(string $name, mixed $contents): array
    {
        return [
            'name' => $name,
            'contents' => $contents,
        ];
    }
}
