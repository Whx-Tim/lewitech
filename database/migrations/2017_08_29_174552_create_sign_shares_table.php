<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_shares', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->index()->comment('发起补签用户id');
            $table->unsignedInteger('help_id')->default(0)->index()->comment('帮助补签用户id');
            $table->unsignedInteger('sign_timer_id')->default(0)->index()->comment('签到周期id');
            $table->foreign('sign_timer_id')->references('id')->on('sign_timers')->onDelete('cascade');
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
        Schema::dropIfExists('sign_shares');
    }
}
