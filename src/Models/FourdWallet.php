<?php

namespace Falconeri\FourdWallet\Models;

use Falconeri\FourdWallet\Services\WalletService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class FourdWallet extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'name',
        'slug',
        'description',
        'raw_balance'
    ];

    protected $casts = [
        'raw_balance' => 'float'
    ];

    /**
     * @param  string  $name
     * @return void
     */
    public function setNameAttribute(string $name)
    {
        $this->attributes['name'] = $name;

        /**
         * Must be updated only if the model does not exist
         *  or the slug is empty
         */
        if (!$this->exists && !array_key_exists('slug', $this->attributes)) {
            $this->attributes['slug'] = Str::slug(Str::lower($name));
        }
    }

    /**
     * Retrieve user
     *
     * @return mixed
     */
    public function user()
    {
        return $this->morphTo('user');
    }

    /**
     * Retrieve all transactions
     *
     * @return mixed
     */
    public function transactions()
    {
        return $this->hasMany(FourdWalletTransaction::class);
    }

    public function getBalanceAttribute()
    {
        return is_null($this->raw_balance) ? (float) 0 : $this->raw_balance;
    }

    /**
     * Determine if the user can withdraw the given amount
     * @param  integer  $amount
     * @return boolean
     */
    public function canWithdraw($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * @param  integer  $amount
     * @param  null  $remark
     * @param  array  $meta
     * @param  bool  $confirmed
     * @return mixed
     */
    public function deposit($amount, $remark = null, $meta = [], $confirmed = true)
    {
        $self = $this;
        return app(WalletService::class)->deposit($self, $amount, $remark, $meta, $confirmed);
    }

    /**
     * @param  integer  $amount
     * @param  null  $remark
     * @param  array  $meta
     * @param  bool  $confirmed
     * @return mixed
     */
    public function withdraw($amount, $remark = null, $meta = [], $confirmed = true)
    {
        $self = $this;
        app(WalletService::class)->verifyWithdraw($self, $amount);
        return app(WalletService::class)->withdraw($self, $amount, $remark, $meta, $confirmed);
    }

    /**
     * @param  FourdWallet  $walletTo
     * @param  integer  $amount
     * @param  null  $remark
     * @param  array  $meta
     * @return mixed
     */
    public function transfer($walletTo, $amount, $remark = null, $meta = [])
    {
        $self = $this;
        return app(WalletService::class)->transfer($self, $walletTo, $amount, $remark, $meta);
    }

    /**
     * Returns the actual balance for this wallet.
     * Might be different from the balance property if the database is manipulated
     * @return float balance
     */
    public function actualBalance()
    {
        $credits = $this->transactions()
            ->where('type', FourdWalletTransaction::TYPE_DEPOSIT)
            ->where('confirmed', 1)
            ->sum('amount');

        $debits = $this->transactions()
            ->where('type', FourdWalletTransaction::TYPE_WITHDRAW)
            ->where('confirmed', 1)
            ->sum('amount');

        return (float) $credits - $debits;
    }


    /**
     * @return bool
     */
    public function refreshBalance(): bool
    {
        return app(WalletService::class)->refresh($this);
    }
}
