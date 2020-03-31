<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaySignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_signs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->unsignedInteger('reward')->default(0)->comment('奖金');
            $table->unsignedInteger('amount')->default(0)->comment('参与人数');
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
        Schema::dropIfExists('day_signs');
    }
}
