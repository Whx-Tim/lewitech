<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsHistroysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_histroys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone')->nullable()->comment('目标手机');
            $table->tinyInteger('status')->default(0)->comment('是否成功');
            $table->text('ip')->nullable()->comment('发起用户ip地址');
            $table->text('message')->nullable()->comment('返回消息');
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
        Schema::dropIfExists('sms_histroys');
    }
}
