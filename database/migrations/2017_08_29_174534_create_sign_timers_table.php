<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('status')->default(0)->comment('本轮周期报名状态');
            $table->unsignedInteger('day')->default(0)->comment('本轮周期签到天数');
            $table->unsignedInteger('apply_count')->default(0)->comment('参与人数');
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
        Schema::dropIfExists('sign_timers');
    }
}
