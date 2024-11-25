<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/workbench',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_83);

    $rectorConfig->sets([
        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
        SetList::PHP_83,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::INSTANCEOF,
        PHPUnitSetList::PHPUNIT_100
    ]);

    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.neon.dist');
};
