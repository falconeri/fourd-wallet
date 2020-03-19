<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourdWalletsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fourd_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('user');
            $table->string('name');
            $table->string('slug')->index();
            $table->string('description')->nullable();
            $table->decimal('raw_balance', 12, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_type', 'user_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fourd_wallets');
    }
}
