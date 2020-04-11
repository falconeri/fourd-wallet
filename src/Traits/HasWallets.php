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
     * @param  null  $slug
     * @return mixed
     */
    public function wallet($name = null)
    {
        $wallets = $this->wallets();

        if (is_null($name)) {
            $slug = 'default';
            $wallets = $wallets->where('slug', $slug);
        } elseif (is_int($name)) {
            $slug = Str::slug(Str::random('9'));
            $id = (int) $name;
            $wallets = $wallets->where('id', $id);
        } elseif (is_string($name)) {
            $slug = Str::slug($name);
            $wallets = $wallets->where('slug', $slug);
        } else {
            $slug = 'default';
            $wallets = $wallets->where('slug', $slug);
        }

        $wallets = $wallets->firstOrCreate(['name' => $name, 'slug' => $slug]);

        return $wallets;
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
