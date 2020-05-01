<?php

use Falconeri\FourdWallet\Models\FourdWalletTransfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourdWalletTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fourd_wallet_transfers', function (Blueprint $table) {
            $enums = [
                FourdWalletTransfer::STATUS_EXCHANGE,
                FourdWalletTransfer::STATUS_TRANSFER,
                FourdWalletTransfer::STATUS_PAID,
                FourdWalletTransfer::STATUS_REFUND,
                FourdWalletTransfer::STATUS_GIFT,
            ];

            $table->bigIncrements('id');
            $table->morphs('from');
            $table->morphs('to');
            $table->enum('status', $enums)->default(FourdWalletTransfer::STATUS_TRANSFER);
            $table->enum('status_last', $enums)->nullable();
            $table->unsignedBigInteger('deposit_id');
            $table->unsignedBigInteger('withdraw_id');
            $table->decimal('fee', 12, 4);
            $table->integer('fee_percentage')->default(0);
            $table->decimal('bonus', 12, 4);
            $table->integer('bonus_percentage')->default(0);
            $table->uuid('uuid')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('deposit_id')
                ->references('id')
                ->on('fourd_wallet_transactions')
                ->onDelete('cascade');

            $table->foreign('withdraw_id')
                ->references('id')
                ->on('fourd_wallet_transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fourd_wallet_transfers');
    }
}
