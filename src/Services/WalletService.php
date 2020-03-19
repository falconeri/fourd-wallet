<?php


namespace Falconeri\FourdWallet\Services;

use Falconeri\FourdWallet\Exceptions\BalanceIsEmpty;
use Falconeri\FourdWallet\Exceptions\InsufficientFunds;
use Falconeri\FourdWallet\Models\FourdWalletTransfer;
use Falconeri\FourdWallet\Models\FourdWalletTransaction;
use Falconeri\FourdWallet\Models\FourdWallet;
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

        $transfers = app(FourdWalletTransfer::class)->create([
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

        return $transfers;
    }

    public function deposit($wallet, $amount, $remark = null, $meta = [], $confirmed = true)
    {
        if ($confirmed) {
            $wallet->raw_balance += $amount;
            $wallet->save();
        } elseif (!$wallet->exists) {
            $wallet->save();
        }

        $transactions = $wallet->transactions()
            ->create([
                'amount' => $amount,
                'uuid' => Uuid::uuid4()->toString(),
                'type' => FourdWalletTransaction::TYPE_DEPOSIT,
                'confirmed' => $confirmed,
                'meta' => $meta,
                'remark' => $remark
            ]);

        return $transactions;
    }

    public function withdraw($wallet, $amount, $remark = null, $meta = [], $confirmed = true)
    {
        $accepted = $confirmed ? $wallet->canWithdraw($amount) : true;

        if ($accepted) {
            $wallet->raw_balance -= $amount;
            $wallet->save();
        } elseif (!$wallet->exists) {
            $wallet->save();
        }

        $transactions = $wallet->transactions()
            ->create([
                'amount' => $amount,
                'uuid' => Uuid::uuid4()->toString(),
                'type' => FourdWalletTransaction::TYPE_WITHDRAW,
                'confirmed' => $accepted,
                'meta' => $meta,
                'remark' => $remark
            ]);

        return $transactions;
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
}
