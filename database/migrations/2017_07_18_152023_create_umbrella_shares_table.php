<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmbrellaSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umbrella_shares', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->comment('发起分享用户id');
            $table->unsignedInteger('friend_id')->nullable()->comment('朋友用户id');
            $table->unsignedTinyInteger('is_register')->nullable()->default(0)->comment('朋友是否注册');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('状态');
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
        Schema::dropIfExists('umbrella_shares');
    }
}
