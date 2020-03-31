<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('status')->default(0)->comment('签到状态');
            $table->unsignedInteger('total_count')->default(0)->comment('总签到次数');
            $table->unsignedInteger('duration_count')->default(0)->comment('持续签到次数');
            $table->unsignedTinyInteger('is_free')->default(0)->comment('是否免费参与本轮签到');
            $table->unsignedTinyInteger('is_apply')->default(0)->comment('是否报名参与本轮签到');

            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('sign_infos');
    }
}
