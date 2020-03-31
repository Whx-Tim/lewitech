<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignDonatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_donates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('姓名');
            $table->string('phone')->nullable()->comment('联系电话');
            $table->string('type')->nullable()->comment('付款方式');

            $table->unsignedInteger('user_id')->nullable()->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('wechat_deal_id')->nullable()->comment('捐款外键');

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
        Schema::dropIfExists('sign_donates');
    }
}
