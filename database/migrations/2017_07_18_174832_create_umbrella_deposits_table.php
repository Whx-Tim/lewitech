<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmbrellaDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umbrella_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->double('money',10,2)->nullable()->default(0.00)->comment('缴纳押金金额');
            $table->string('type')->nullable()->comment('缴纳类型');
            $table->string('status')->nullable()->comment('交易状态');
            $table->unsignedTinyInteger('is_effective')->nullable()->comment('押金生效状态');
            $table->dateTime('pay_at')->nullable()->comment('缴纳时间');
            $table->string('order_number')->nullable()->comment('订单编号');
            $table->unsignedInteger('user_id')->nullable()->comment('缴纳用户id');
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
        Schema::dropIfExists('umbrella_deposits');
    }
}
