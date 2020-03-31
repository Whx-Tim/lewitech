<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('status')->default(0)->comment('核销状态');
            $table->timestamp('start_at')->nullable()->comment('开始有效期');
            $table->timestamp('end_at')->nullable()->comment('结束有效期');

            $table->unsignedInteger('user_id')->default(0)->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('card_id')->default(0)->comment('卡券外键');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sign_cards');
    }
}
