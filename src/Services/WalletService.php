<?php


namespace Falconeri\FourdWallet\Services;

use Falconeri\FourdWallet\Exceptions\BalanceIsEmpty;
use Falconeri\FourdWallet\Exceptions\InsufficientFunds;
use Falconeri\FourdWallet\Models\FourdWallet;
use Falconeri\FourdWallet\Models\FourdWalletTransfer;
use Falconeri\FourdWallet\Models\FourdWalletTransaction;
use Ramsey\Uuid\Uuid;

class WalletService
{
    public function transfer(
        $from,
        $to,
        $amount,
        $remark = null,
        $meta = [],
        $status = FourdWalletTransfer::STATUS_TRANSFER
    ) {
        $this->verifyWithdraw($from, $amount);
        return $this->forceTransfer($from, $to, $amount, $remark, $meta, $status);
    }

    public function forceTransfer(
        $from,
        $to,
        $amount,
        $remark = null,
        $meta = [],
        $status = FourdWalletTransfer::STATUS_TRANSFER
    ) {
        $withdraw = $this->withdraw($from, $amount, $remark, $meta);
        $deposit = $this->deposit($to, $amount, $remark, $meta);

        return app(FourdWalletTransfer::class)->create([
            'status' => $status,
            'deposit_id' => $deposit->getKey(),
            'withdraw_id' => $withdraw->getKey(),
            'from_id' => $from->getKey(),
            'from_type' => $from->getMorphClass(),
            'to_id' => $to->getKey(),
            'to_type' => $to->getMorphClass(),
            'uuid' => Uuid::uuid4()->toString(),
            'fee' => 0
        ]);
    }

    public function deposit($wallet, $amount, $remark = null, $meta = [], $confirmed = true)
    {
        if ($confirmed) {
            $wallet->raw_balance += $amount;
            $wallet->save();
        } elseif (!$wallet->exists) {
            $wallet->save();
        }

        return $wallet->transactions()
            ->create([
                'amount' => $amount,
                'uuid' => Uuid::uuid4()->toString(),
                'type' => FourdWalletTransaction::TYPE_DEPOSIT,
                'confirmed' => $confirmed,
                'meta' => $meta,
                'remark' => $remark
            ]);
    }

    public function withdraw($wallet, $amount, $remark = null, $meta = [], $confirmed = true)
    {
        $accepted = $confirmed ? $wallet->canWithdraw($amount) : false;

        if ($accepted) {
            $wallet->raw_balance -= $amount;
            $wallet->save();
        } elseif (!$wallet->exists) {
            $wallet->save();
        }

        return $wallet->transactions()
            ->create([
                'amount' => $amount,
                'uuid' => Uuid::uuid4()->toString(),
                'type' => FourdWalletTransaction::TYPE_WITHDRAW,
                'confirmed' => $accepted,
                'meta' => $meta,
                'remark' => $remark
            ]);
    }

    public function verifyWithdraw($wallet, $amount)
    {
        if ($amount && !$wallet->balance) {
            throw new BalanceIsEmpty('Wallet is empty');
        }

        if (!$wallet->canWithdraw($amount)) {
            throw new InsufficientFunds('Insufficient funds');
        }
    }

    public function refresh($wallet)
    {
        $balance = $wallet->actualBalance();
        $wallet->raw_balance = $balance;

        return $wallet->save();
    }

    /**
     * @param  FourdWallet  $wallet
     * @param  FourdWalletTransaction  $transaction
     * @return bool
     */
    public function updateBalance(FourdWallet $wallet, FourdWalletTransaction $transaction): bool
    {
        $balance = ['raw_balance' => $wallet->raw_balance];

        if ($transaction->type === FourdWalletTransaction::TYPE_WITHDRAW) {
            $balance['raw_balance'] -= abs($transaction->amount);
        }

        if ($transaction->type === FourdWalletTransaction::TYPE_DEPOSIT) {
            $balance['raw_balance'] += abs($transaction->amount);
        }

        return $wallet->update(compact('balance'));
    }
}
