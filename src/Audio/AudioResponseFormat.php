<?php

declare(strict_types=1);

namespace Openai\Audio;

/**
 * @method static self tryFrom(mixed $format)
 * @property string $value
 */
enum AudioResponseFormat: string
{
    case JSON = 'json';
    case TEXT = 'text';
    case SRT = 'srt';
    case VERBOSE_JSON = 'verbose_json';
    case VTT = 'vtt';
}
