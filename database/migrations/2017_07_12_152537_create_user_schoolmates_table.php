<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSchoolmatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_schoolmates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('student_number')->nullable()->comment('学号');
            $table->string('college')->nullable()->comment('学院');
            $table->string('grade')->nullable()->comment('入学年份');
            $table->unsignedInteger('user_id')->nullable()->comment('用户外键id');
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
        Schema::dropIfExists('user_schoolmates');
    }
}
