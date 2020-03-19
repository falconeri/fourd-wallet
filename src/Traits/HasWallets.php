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
        $slug = Str::slug($name);

        if (is_null($slug)) {
            $slug = 'default';
        } elseif (is_string($slug)) {
            $slug = $slug;
        } else {
            $slug = 'default';
        }

        return $this->wallets()->where('slug', $slug)->firstOrCreate(['name' => $name, 'slug' => $slug]);
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
