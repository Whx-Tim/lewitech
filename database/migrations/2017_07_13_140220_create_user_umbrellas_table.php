<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUmbrellasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_umbrellas', function (Blueprint $table) {
            $table->increments('id');
//            $table->unsignedTinyInteger('is_donate')->nullable()->default(0)->comment('是否捐过款');
//            $table->unsignedTinyInteger('is_deposit')->nullable()->default(0)->comment('是否缴纳押金');
//            $table->unsignedTinyInteger('is_have')->nullable()->default(0)->comment('是否有伞');
//            $table->unsignedInteger('have_amount')->nullable()->default(0)->comment('拥有伞的数量');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('借伞状态');
            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键id');
            $table->unsignedInteger('force_count')->default(0)->comment('强制还伞次数');
            $table->dateTime('borrow_at')->nullable()->comment('借伞时间');
            $table->dateTime('still_at')->nullable()->comment('还伞时间');
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
        Schema::dropIfExists('user_umbrellas');
    }
}
