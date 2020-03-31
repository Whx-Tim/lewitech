<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('score')->default(0)->comment('积分变动幅度');
            $table->unsignedTinyInteger('type')->default(0)->comment('积分类型');
            $table->string('operation', 10)->comment('增减操作');
            $table->string('operation_type')->nullable()->comment('操作类型');
            $table->string('operation_notice')->nullable()->comment('操作说明');
            $table->unsignedInteger('user_id')->default(0)->comment('用户外键');
            $table->softDeletes();
            $table->timestamps();

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
        Schema::dropIfExists('score_histories');
    }
}
