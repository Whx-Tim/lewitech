<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('get_ups', function (Blueprint $table) {
//            $table->increments('id');
//            $table->unsignedInteger('user_id');
//            $table->dateTime('last_get_up_datetime')->nullable()->comment('最后签到时间');
//            $table->unsignedInteger('day_duration')->nullable()->comment('签到持续时间');
//            $table->unsignedInteger('day_sum')->nullable()->comment('签到总时间');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('get_ups');
    }
}
