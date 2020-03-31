<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('活动名称');
            $table->text('poster')->comment('活动封面图片');
            $table->string('sponsor')->comment('主办方');
            $table->string('phone')->comment('主办方联系电话');
            $table->string('location')->comment('活动位置');
            $table->longText('images')->nullable()->comment('活动图片');
            $table->dateTime('time')->nullable()->comment('活动时间');
//            $table->unsignedInteger('view_count')->nullable()->default(0)->comment('浏览数');
//            $table->unsignedInteger('apply_count')->nullable()->default(0)->comment('参与数');
            $table->float('lat',15,10)->nullable()->comment('位置纬度');
            $table->float('lng',15,10)->nullable()->comment('位置经度');
            $table->float('apply_money',10,2)->nullable()->default(0.00)->comment('报名金额');
            $table->text('description')->nullable()->comment('活动描述');
            $table->dateTime('end_at')->nullable()->comment('截止时间');
            $table->string('persons')->nullable()->default(0)->comment('人数限制');
            $table->text('invite_condition')->nullable()->comment('邀请条件');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('活动状态');
            $table->unsignedInteger('user_id')->nullable()->comment('创建用户id');
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
        Schema::dropIfExists('actives');
    }
}
