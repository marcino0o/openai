<?php

declare(strict_types=1);

namespace Openai\Utils;

use DateTimeImmutable;

readonly class DateTimeUtils
{
    public static function fromTimestamp(int $timestamp): DateTimeImmutable
    {
        $datetime = new DateTimeImmutable();

        return $datetime->setTimestamp($timestamp);
    }
}
