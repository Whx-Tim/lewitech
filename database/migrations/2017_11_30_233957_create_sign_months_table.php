<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_months', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable()->comment('本轮签到状态');
            $table->unsignedTinyInteger('total_day')->default(0)->nullable()->comment('本轮一共签到天数');
            $table->unsignedTinyInteger('duration_day')->default(0)->nullable()->comment('本轮持续签到天数');
            $table->unsignedInteger('time_value')->default(0)->nullable()->comment('本轮早起值');
            $table->double('reward', 10, 2)->default(0)->nullable()->comment('本轮获得奖金');

            $table->unsignedInteger('user_id')->nullable()->default(0)->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('timer_id')->nullable()->index()->deafult(0)->comment('签到周期外键');

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
        Schema::dropIfExists('sign_months');
    }
}
