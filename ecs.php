<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecs): void {
    $ecs->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecs->sets([
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::ARRAY,
        SetList::STRICT,
        SetList::DOCBLOCK,
    ]);

    $ecs->skip([
        __DIR__ . '/vendor/*',
    ]);

    // Jeśli chcesz krótsze importy i jednolity order:
    $ecs->ruleWithConfiguration(PhpCsFixer\Fixer\Import\OrderedImportsFixer::class, [
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha',
    ]);
};
