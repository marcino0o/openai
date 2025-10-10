<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/vendor',
    ]);

    // Podbijanie do nowszych standardów PHP (dostosuj do minimalnego PHP w paczce)
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::DEAD_CODE,
    ]);

    // Bezpieczniej: dry-run w CI, real process lokalnie
};
