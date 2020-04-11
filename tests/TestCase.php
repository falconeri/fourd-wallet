<?php


namespace Falconeri\FourdWallet\Tests;

use Falconeri\FourdWallet\FourdWalletServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->loadMigrationsFrom([
            '--path' => dirname(__DIR__) . '/database/migrations'
        ]);
        $this->withFactories(__DIR__ . '/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            FourdWalletServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
        include_once __DIR__ . '/migrations/create_users_table.php.stub';

        // run the up() method (perform the migration)
        (new \CreateUsersTable())->up();
    }
}
