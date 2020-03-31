<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('校企名称');
            $table->string('phone')->nullable()->comment('校企联系电话');
            $table->string('linkman', 40)->nullable()->comment('联系人');
            $table->string('linkman_phone', 15)->nullable()->comment('联系人电话');
            $table->string('address')->nullable()->comment('校企地址');
            $table->unsignedTinyInteger('type')->default(0)->index()->comment('校企类型');
            $table->unsignedTinyInteger('status')->default(0)->index()->comment('校企状态');
            $table->string('poster')->nullable()->comment('校企宣传海报图片路径');
            $table->double('score')->default(0)->comment('校企评分');
            $table->double('price')->default(0)->comment('消费人均');
            $table->float('lat', 20, 10)->default(0)->comment('纬度');
            $table->float('lng', 20, 10)->default(0)->comment('经度');
            $table->longText('image')->nullable()->comment('图片路径集合');
            $table->text('introduction')->nullable()->comment('校企简介');
            $table->longText('detail')->nullable()->comment('校企详情描述');
            $table->text('discount')->nullable()->comment('商家优惠折扣描述');
            $table->string('discount_date')->nullable()->comment('商家优惠时段');
            $table->text('share')->nullable()->comment('商家分享内容');

            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键id');

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
        Schema::dropIfExists('businesses');
    }
}
