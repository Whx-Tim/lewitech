<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('badge_id')->default(0)->comment('徽章外键');
            $table->unsignedInteger('user_id')->default(0)->comment('用户外键');
            $table->text('data')->nullable()->comment('额外添加的json信息');
            $table->timestamps();

            $table->index('badge_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badge_users');
    }
}
