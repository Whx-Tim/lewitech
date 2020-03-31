<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePalettesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('palettes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('sky_index')->default(1)->comment('星空序号');
            $table->text('source')->nullable()->comment('图片源');
            $table->text('month')->nullable()->comment('月亮图片路径');
            $table->text('description')->nullable()->comment('描述内容');

            $table->unsignedInteger('user_id')->default(0)->index()->comment('用户外键');
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
        Schema::dropIfExists('palettes');
    }
}
