<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('徽章名称');
            $table->string('type')->nullable()->comment('徽章类型');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->text('badge_url')->nullable()->comment('徽章原图');
            $table->text('remote_url')->nullable()->comment('徽章外框图');
            $table->text('local_url')->nullable()->comment('徽章外框本地图');
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
        Schema::dropIfExists('badges');
    }
}
