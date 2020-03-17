<?php

use Falconeri\FourdWallet\FourdWalletTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourdWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fourd_wallet_transactions', function (Blueprint $table) {
            $enums = [
                FourdWalletTransaction::TYPE_DEPOSIT,
                FourdWalletTransaction::TYPE_WITHDRAW,
            ];

            $table->bigIncrements('id');
            $table->unsignedBigInteger('fourd_wallet_id');
            $table->enum('type', $enums)->index();
            $table->decimal('amount', 12, 4);
            $table->boolean('confirmed');
            $table->json('meta')->nullable();
            $table->uuid('uuid')->unique();
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fourd_wallet_id')->references('id')->on('fourd_wallets')->onDelete('cascade');
        });
    }

    /**
     * @param  Blueprint  $table
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function json(Blueprint $table, string $column)
    {
        $conn = DB::connection();
        if ($conn instanceof MySqlConnection || $conn instanceof PostgresConnection) {
            $pdo = $conn->getPdo();
            try {
                $sql = 'SELECT JSON_EXTRACT(\'[10, 20, [30, 40]]\', \'$[1]\');';
                $prepare = $pdo->prepare($sql);
                $prepare->fetch();
            } catch (\Throwable $throwable) {
                return $table->text($column);
            }
        }

        return $table->json($column);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fourd_wallet_transactions');
    }
}
