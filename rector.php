<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\NoSetupWithParentCallOverrideRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/workbench',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_83);
    $rectorConfig->parallel(120, 6, 80);

    $rectorConfig->skip([
        NoSetupWithParentCallOverrideRector::class,
        PreferPHPUnitThisCallRector::class,
        RenameMethodRector::class => [
            __DIR__.'/tests/Feature/WithQueryTest.php',
        ],
    ]);
    $rectorConfig->rule(PreferPHPUnitSelfCallRector::class);

    $rectorConfig->sets([
        PHPUnitSetList::COMPOSER_BASED,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::PHP_83,
        SetList::TYPE_DECLARATION,
    ]);

    $rectorConfig->cacheClass(FileCacheStorage::class);
    $rectorConfig->cacheDirectory(__DIR__.'/storage/tmp/rector');
    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.neon.dist');
};
