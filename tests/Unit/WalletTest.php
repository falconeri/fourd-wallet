<?php


namespace Falconeri\FourdWallet\Tests\Unit;


use Falconeri\FourdWallet\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Falconeri\FourdWallet\Tests\Models\User;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function testWallet()
    {
        $user = factory(User::class)->create();
        // test find wallet without name
        $wallet = $user->wallet();
        $this->assertEquals('default', $wallet->name);
        $this->assertEquals('default', $wallet->slug);
        $this->assertEquals(0, $wallet->balance);

        // test find wallet by name
        $wallet2 = $user->wallet('Test User');
        $this->assertEquals('Test User', $wallet2->name);
        $this->assertEquals('test-user', $wallet2->slug);
        $this->assertEquals(0, $wallet2->balance);

        // test find wallet by id
        $wallet3 = $user->wallet(2);
        $this->assertEquals('Test User', $wallet3->name);
        $this->assertEquals('test-user', $wallet3->slug);
        $this->assertEquals(0, $wallet3->balance);
    }
}
