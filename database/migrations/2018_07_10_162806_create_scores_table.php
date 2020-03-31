<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('type')->default(0)->comment('积分类型');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->bigInteger('score')->default(0)->comment('积分');
            $table->unsignedInteger('user_id')->default(0)->comment('用户外键');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scores');
    }
}
