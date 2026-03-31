<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use Sylarele\PhpCsFixerConfig\Config;

$finder = Finder::create()
    ->exclude('storage')
    ->in(__DIR__)
    ->append([
        __DIR__.'/rector.php',
        __DIR__.'/structura.php',
        __FILE__,
    ]);

$config = new Config();

return $config
    ->setCacheFile(__DIR__.'/storage/tmp/php-cs-fixer/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setParallelConfig(new ParallelConfig(6, 80));
