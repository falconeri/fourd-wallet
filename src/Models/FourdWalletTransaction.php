<?php

namespace Falconeri\FourdWallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FourdWalletTransaction extends Model
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    /**
     * @var array
     */
    protected $fillable = [
        'fourd_wallet_id',
        'type',
        'amount',
        'confirmed',
        'meta',
        'uuid',
        'remark'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'fourd_wallet_id' => 'int',
        'confirmed' => 'bool',
        'meta' => 'json'
    ];


    /**
     * @return BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(FourdWallet::class);
    }

    /**
     * Retrieve the amount with the positive or negative sign
     *
     * @return string
     */
    public function getAmountWithSignAttribute()
    {
        return in_array($this->type, [self::TYPE_DEPOSIT])
            ? '+'.$this->amount
            : '-'.$this->amount;
    }
}
