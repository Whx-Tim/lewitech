<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->text('head_img')->nullable()->comment('用户头像');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('sex')->nullable()->comment('性别');
            $table->string('city')->nullable()->comment('所在城市');
            $table->string('country')->nullable()->comment('所在国家');
            $table->string('language')->nullable()->comment('用户所用语言');
            $table->tinyInteger('subscribe')->nullable()->comment('用户是否关注该公众号，0：没有关注');
            $table->string('subscribe_time')->nullable()->comment('用户最后关注公众号时间');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->string('name')->nullable()->comment('真实姓名');
            $table->softDeletes();
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
        Schema::dropIfExists('wechat_user_details');
    }
}
