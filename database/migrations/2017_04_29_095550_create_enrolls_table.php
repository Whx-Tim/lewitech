<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('报名者姓名');
            $table->string('phone')->nullable()->comment('报名者联系方式');
            $table->longText('data')->nullable()->comment('报名表单json');
            $table->unsignedTinyInteger('status')->nullable()->default(0)->comment('报名状态');
            $table->unsignedInteger('user_id')->nullable()->comment('报名用户Id');
            $table->morphs('enrollable');
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
        Schema::dropIfExists('enrolls');
    }
}
