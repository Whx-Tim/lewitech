<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->default(0)->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('deal_id')->nullable()->default(0)->comment('交易记录外键');
            $table->foreign('deal_id')->references('id')->on('wechat_deals')->onDelete('set null');

            $table->unsignedTinyInteger('status')->default(0)->comment('我的状态');
            $table->string('name', 30)->nullable()->default('-')->comment('姓名');
            $table->string('id_number')->nullable()->default('-')->comment('身份证号');
            $table->unsignedTinyInteger('type')->default(0)->comment('互助类型');
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
        Schema::dropIfExists('help_users');
    }
}
