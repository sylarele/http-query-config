<?php

declare(strict_types=1);

use StructuraPhp\Structura\Contracts\StructuraConfigInterface;

return static function (StructuraConfigInterface $config): void {
    $config
        ->addTestSuite('tests/Architecture', 'main')
        ->archiRootNamespace(
            'Sylarele\\HttpQueryConfig\\Tests\\Architecture',
            'tests/Architecture'
        );
};
