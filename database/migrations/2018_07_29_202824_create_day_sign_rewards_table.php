<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaySignRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_sign_rewards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status')->default(0)->comment('处理状态');
            $table->unsignedInteger('user_id')->default(0)->comment('用户外键id');
            $table->unsignedInteger('day_sign_id')->default(0)->comment('打卡轮次id');
            $table->string('reward')->default(0)->comment('获得奖金');
            $table->timestamps();

            $table->index('user_id');
            $table->index('day_sign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_sign_rewards');
    }
}
