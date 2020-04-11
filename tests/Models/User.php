<?php


namespace Falconeri\FourdWallet\Tests\Models;

use Falconeri\FourdWallet\Traits\HasWallets;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasWallets;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'password_string', 'remember_token',
    ];
}
