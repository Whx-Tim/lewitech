<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_red_packs', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable()->comment('用户外键id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->string('return_code', 16)->nullable()->comment('返回状态码');
            $table->string('return_msg', 128)->nullable()->comment('返回信息');
            $table->string('result_code', 16)->nullable()->comment('业务结果');
            $table->string('mch_bill_no', 28)->unquie()->comment('商户订单号，唯一');
            $table->string('openid', 32)->comment('发送的openid');
            $table->integer('total_amount')->comment('红包金额，单位分');
            $table->string('send_list_id', 32)->comment('微信返回的红包订单号');

            $table->string('err_code', 32)->nullable()->comment('错误代码');
            $table->string('err_code_des', 128)->nullable()->comment('错误信息描述');

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
        Schema::dropIfExists('red_packs');
    }
}
