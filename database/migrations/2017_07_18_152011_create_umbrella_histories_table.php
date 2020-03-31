<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmbrellaHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umbrella_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键id');
            $table->unsignedInteger('umbrella_id')->default(0)->index()->comment('伞外键id');
            $table->unsignedInteger('form_id')->nullable()->comment('流转来源id');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('状态');
            $table->dateTime('borrow_at')->nullable()->comment('借出时间');
            $table->dateTime('still_at')->nullable()->comment('归还时间');
            $table->string('borrow_station')->nullable()->comment('借出站点');
            $table->string('still_station')->nullable()->comment('归还站点');
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
        Schema::dropIfExists('umbrella_histories');
    }
}
