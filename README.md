# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/falconeri/fourd-wallet.svg?style=flat-square)](https://packagist.org/packages/falconeri/fourd-wallet)
[![Build Status](https://img.shields.io/travis/falconeri/fourd-wallet/master.svg?style=flat-square)](https://travis-ci.org/falconeri/fourd-wallet)
[![Quality Score](https://img.shields.io/scrutinizer/g/falconeri/fourd-wallet.svg?style=flat-square)](https://scrutinizer-ci.com/g/falconeri/fourd-wallet)
[![Total Downloads](https://img.shields.io/packagist/dt/falconeri/fourd-wallet.svg?style=flat-square)](https://packagist.org/packages/falconeri/fourd-wallet)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require falconeri/fourd-wallet
```

You can publish the migration with:
```bash
php artisan vendor:publish --tag="fourd-wallet-migrations"
```

After the migration file has been published you can create the wallet-plus tables by running the migration:
```bash
php artisan migrate
```

## Usage

First, you'll need to add the HasWallets trait to your model.
``` php
use Falconeri\FourdWallet\Traits\HasWallets;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasWallets;
}
```

You can start by call a wallet for given user.
```php
$user = User::find(1);

$wallet = $user->wallet('My new wallet'); // it will create a new wallet if not found
$wallet->balance; // float(0) if new wallet
```
You can set up multiple types of wallets by simply create a wallet with name
```php
$user = User::find(1);

$user->hasWallet('Wallet 1'); // bool(false)
$user->hasWallet('Wallet 2'); // bool(false)

$wallet1 = $user->wallet('Wallet 1'); 
$wallet2 = $user->wallet('Wallet 2'); 

$user->hasWallet('Wallet 1'); // bool(true)
$user->hasWallet('Wallet 2'); // bool(true)
```

User can make a transfer between wallet
```php
$first = User::first(); 
$last = User::orderBy('id', 'desc')->first(); // last user

$wallet1 = $first->wallet('Wallet 1'); 
$wallet2 = $last->wallet('Wallet 2'); 

$first->balance; // int(100)
$last->balance; // int(0)

$wallet1->transfer($wallet2, 5); 
$first->balance; // int(95)
$last->balance; // int(5)
```
### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email falconeriata@gmail.com instead of using the issue tracker.

## Credits

- [Alta Falconeri](https://github.com/falconeri)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
