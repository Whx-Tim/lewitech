<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQrcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qrcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('value')->comment('二维码的内容值');
            $table->text('url')->nullable()->comment('二维码可查看url');
            $table->text('path')->nullable()->comment('保存在本地的路径');
            $table->unsignedTinyInteger('type')->default(0)->comment('类型');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->text('ticket')->nullable()->comment('微信-二维码票码');
            $table->unsignedInteger('expire_seconds')->default(0)->comment('微信-有效期，秒');
            $table->string('scene_str', 65)->default('-')->index()->comment('微信-场景值');
            $table->string('action_name')->nullable()->comment('微信定义的类型名称');
            $table->text('description')->nullable()->comment('描述说明');

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
        Schema::dropIfExists('qrcodes');
    }
}
