<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmbrellasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umbrellas', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('bind_at')->nullable()->comment('绑定时间');
            $table->dateTime('borrow_at')->nullable()->comment('借伞时间');
            $table->dateTime('still_at')->nullable()->comment('还伞时间');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('雨伞状态');
            $table->unsignedInteger('user_id')->default(0)->index()->comment('绑定用户外键id');
            $table->unsignedInteger('owner_id')->nullable()->comment('捐款用户id');
            $table->longText('data')->nullable()->comment('捐赠信息json');
            $table->unsignedInteger('station_id')->nullalbe()->comment('站点id');
            $table->unsignedInteger('scan_count')->default(0)->comment('扫码次数');
            $table->unsignedInteger('real_scan_count')->default(0)->comment('有效扫码次数');
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
        Schema::dropIfExists('umbrellas');
    }
}
