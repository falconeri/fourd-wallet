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
        $where = 'slug';

        if (is_null($name)) {
            $whereCon = $slug = $name = 'default';
        } elseif (is_int($name)) {
            $where = 'id';
            $whereCon = (int) $name;
            $slug = $name = Str::slug(Str::random('9'));
        } elseif (is_string($name)) {
            $whereCon = $slug = Str::slug($name);
        } else {
            $whereCon = $slug = $name = 'default';
        }

        $wallets = $this->wallets()->where($where, $whereCon)->firstOrCreate(['name' => $name, 'slug' => $slug]);

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
