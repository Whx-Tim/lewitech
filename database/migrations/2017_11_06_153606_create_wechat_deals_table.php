<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable()->comment('订单描述');
            $table->unsignedInteger('user_id')->default(0)->nullable()->index()->comment('用户外键');
            $table->string('openid')->comment('用户标识');
            $table->string('result_code', 20)->comment('业务结果，PAID,FAIL,ORDER,BACK,BACK_FAIL,BACK_SUCCESS');
            $table->string('err_code', 32)->nullable()->comment('错误代码');
            $table->string('err_code_des')->nullable()->comment('错误代码描述');
            $table->string('trade_type', 16)->comment('交易类型');
            $table->string('bank_type', 16)->nullable()->default('-')->comment('付款银行');
            $table->unsignedInteger('total_fee')->default(0)->comment('订单总金额，单位分');
            $table->unsignedInteger('cash_fee')->default(0)->comment('现金支付金额，单位分');
            $table->string('transaction_id', 32)->default('-')->index()->comment('微信支付订单号');
            $table->string('out_trade_no', 32)->default('-')->index()->comment('内部订单标识号');
            $table->string('out_refund_no', 64)->nullable()->comment('内部退款订单标识号');
            $table->string('refund_id', 32)->nullable()->comment('微信退款订单号');
            $table->timestamp('refund_at')->nullable()->comment('微信退款发起时间');
            $table->timestamp('paid_at')->nullable()->comment('支付完成时间');
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
        Schema::dropIfExists('wechat_deals');
    }
}
