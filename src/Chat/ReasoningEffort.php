<?php

declare(strict_types=1);

namespace Openai\Chat;

enum ReasoningEffort: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
