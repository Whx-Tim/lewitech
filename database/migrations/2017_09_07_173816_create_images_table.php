<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('图片名称');
            $table->string('size')->nullable()->comment('图片大小');
            $table->string('format')->nullable()->comment('图片格式');
            $table->string('type')->nullable()->comment('图片类型');
            $table->text('path')->nullable()->comment('图片路径');
            $table->longText('data')->nullable()->comment('拓展内容');

            $table->nullableMorphs('imageable');

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
        Schema::dropIfExists('images');
    }
}
