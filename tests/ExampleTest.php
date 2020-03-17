<?php

namespace Falconeri\FourdWallet\Tests;

use Orchestra\Testbench\TestCase;
use Falconeri\FourdWallet\FourdWalletServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [FourdWalletServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
