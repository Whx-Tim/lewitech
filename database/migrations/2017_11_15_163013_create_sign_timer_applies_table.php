<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignTimerAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_timer_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('timer_id')->default(0)->nullable()->comment('签到周期外键');
            $table->foreign('timer_id')->references('id')->on('sign_timers')->onDelete('cascade');
            $table->unsignedInteger('user_id')->default(0)->nullable()->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedTinyInteger('is_free')->default(0)->nullable()->comment('是否免费参与');
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
        Schema::dropIfExists('sign_timer_applies');
    }
}
