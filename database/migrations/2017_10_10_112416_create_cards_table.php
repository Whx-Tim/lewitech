<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('卡名');
            $table->text('description')->nullable()->comment('卡片描述');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->double('regulation', 11, 2)->nullable()->comment('减免比例规则');
            $table->unsignedTinyInteger('regulation_type')->default(0)->comment('卡片规则类型');
            $table->string('duration')->nullable()->comment('持续时长');
            $table->timestamp('start_at')->nullable()->comment('有效期开始');
            $table->timestamp('end_at')->nullable()->comment('有效期结束');
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
        Schema::dropIfExists('cards');
    }
}
