<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignMedalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_medals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gold')->default(0)->comment('金牌数量');
            $table->unsignedInteger('silver')->default(0)->comment('银牌数量');
            $table->unsignedInteger('bronze')->default(0)->comment('铜牌数量');

            $table->unsignedInteger('user_id')->default(0)->nullable()->comment('用户外键');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('sign_medals');
    }
}
