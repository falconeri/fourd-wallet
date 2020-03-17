<?php

namespace Falconeri\FourdWallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FourdWalletTransfer extends Model
{
    public const STATUS_EXCHANGE = 'exchange';
    public const STATUS_TRANSFER = 'transfer';
    public const STATUS_PAID = 'paid';
    public const STATUS_REFUND = 'refund';
    public const STATUS_GIFT = 'gift';

    /**
     * @var array
     */
    protected $fillable = [
        'from_id',
        'from_type',
        'to_id',
        'to_type',
        'status',
        'status_last',
        'deposit_id',
        'withdraw_id',
        'uuid',
        'fee',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'deposit_id' => 'int',
        'withdraw_id' => 'int',
    ];

    /**
     * @return MorphTo
     */
    public function from()
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function to()
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function deposit()
    {
        return $this->belongsTo(FourdWalletTransaction::class, 'deposit_id');
    }

    /**
     * @return BelongsTo
     */
    public function withdraw()
    {
        return $this->belongsTo(FourdWalletTransaction::class, 'withdraw_id');
    }
}
