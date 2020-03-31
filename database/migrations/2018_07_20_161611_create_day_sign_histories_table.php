<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaySignHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_sign_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('time')->nullable()->comment('签到时间');
            $table->unsignedTinyInteger('status')->default(0)->comment('签到状态');
            $table->unsignedInteger('time_value')->default(0)->comment('时间差值');
            $table->unsignedInteger('user_id')->default(0)->comment('用户外键');
            $table->unsignedInteger('day_sign_id')->default(0)->comment('每日打卡外键');
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
        Schema::dropIfExists('day_sign_histories');
    }
}
