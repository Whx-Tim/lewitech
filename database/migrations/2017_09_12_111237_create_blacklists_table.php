<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->string('type')->nullable()->index()->comment('拉黑类型');
            $table->text('description')->nullable()->comment('原因描述');
            $table->longText('data')->nullable()->comment('拓展数据项');

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
        Schema::dropIfExists('blacklists');
    }
}
