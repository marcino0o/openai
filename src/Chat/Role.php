<?php

declare(strict_types=1);

namespace Openai\Chat;

enum Role: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case SYSTEM = 'system';
}
