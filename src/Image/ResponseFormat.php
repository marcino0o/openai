<?php

declare(strict_types=1);

namespace Openai\Image;

/**
 * @property string $value
 */
enum ResponseFormat: string
{
    case URL = 'url';
    case BASE64 = 'b64_json';
}
