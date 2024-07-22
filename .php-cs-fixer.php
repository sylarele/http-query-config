<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude('storage')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'cast_spaces' => ['space' => 'single'],
        'declare_strict_types' => true,
        'global_namespace_import' => [
            'import_constants' => false,
            'import_functions' => false,
        ],
        'heredoc_to_nowdoc' => false,
        'increment_style' => ['style' => 'post'],
        'native_function_invocation' => ['strict' => false],
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'no_trailing_comma_in_singleline' => true,
        'no_unreachable_default_argument_value' => false,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'phpdoc_line_span' => [
            'property' => 'single',
            'const' => 'single',
        ],
        'phpdoc_summary' => false,
        'single_line_throw' => false,
        'yoda_style' => false,
    ])
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/storage/tmp/php-cs-fixer/.php-cs-fixer.cache')
    ->setFinder($finder);
