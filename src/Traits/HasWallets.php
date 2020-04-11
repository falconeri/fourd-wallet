<?php


namespace Falconeri\FourdWallet\Traits;

use Falconeri\FourdWallet\Models\FourdWallet;
use Illuminate\Support\Str;


trait HasWallets
{
    /**
     * method of obtaining all wallets
     *
     * @return mixed
     */
    public function wallets()
    {
        return $this->morphMany(FourdWallet::class, 'user');
    }

    /**
     * method to get wallet by slug
     *
     * @param  null  $name
     * @return mixed
     */
    public function wallet($name = null)
    {
        if (is_null($name)) {
            $slug = $name = 'default';
            return $this->wallets()->firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        if (is_int($name)) {
            $id = (int) $name;
            $slug = $name = Str::slug(Str::random('9'));
            return $this->wallets()->firstOrCreate(['id' => $id], ['name' => $name, 'slug' => $slug]);
        }

        if (is_string($name)) {
            $slug = Str::slug($name);
            return $this->wallets()->firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }

    /**
     * method to check existence of wallet
     *
     * @param $slug
     * @return bool
     */
    public function hasWallet($slug)
    {
        return (bool) $this->wallet($slug);
    }
}
