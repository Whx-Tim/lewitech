<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('today_time')->nullable()->comment('每日签到时间');
            $table->unsignedTinyInteger('today_status')->default(0)->comment('每日签到状态');

            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('sign_timer_id')->default(0)->index()->comment('签到周期外键');

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
        Schema::dropIfExists('signs');
    }
}
