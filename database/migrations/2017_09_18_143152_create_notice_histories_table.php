<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->default('-')->index()->comment('模板消息发送的类别');
            $table->unsignedTinyInteger('status')->default(0)->comment('发送状态');
            $table->longText('data')->nullable()->comment('json拓展项');

            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键');
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
        Schema::dropIfExists('notice_histories');
    }
}
