<?php

declare(strict_types=1);

namespace Openai\Chat;

/**
 * @method static tryFrom(mixed $finish_reason)
 */
enum FinishReason: string
{
    case STOP = 'stop'; // if the model hit a natural stop point or a provided stop sequence
    case LENGTH = 'length'; // if the maximum number of tokens specified in the request was reached
    case FUNCTION_CALL = 'function_call'; // if the model called a function
}
