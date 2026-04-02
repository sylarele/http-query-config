<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * @internal
 */
#[CoversNothing]
class TestCase extends OrchestraTestCase
{
    use WithWorkbench;

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    #[Override]
    protected function defineEnvironment($app)
    {
        /** @var Repository $config */
        $config = $app['config'];
        // Setup default database to use sqlite :memory:
        tap($config, static function (Repository $config): void {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });
    }
}
