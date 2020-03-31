<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->nullable()->comment('评论内容');
            $table->unsignedTinyInteger('status')->default(0)->comment('评论状态');
            $table->longText('data')->nullable()->comment('评论拓展内容');
            $table->nullableMorphs('commentable');

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
        Schema::dropIfExists('comments');
    }
}
