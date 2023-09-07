<?php

declare(strict_types=1);

namespace RWS\Openai\Chat;

/**
 * @property $value
 */
enum Role: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case SYSTEM = 'system';
}
