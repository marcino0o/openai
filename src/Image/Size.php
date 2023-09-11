<?php

declare(strict_types=1);

namespace Openai\Image;

/**
 * @property string $value
 */
enum Size: string
{
    case SMALL = '256x256';
    case MEDIUM = '512x512';
    case LARGE = '1024x1024';
}
