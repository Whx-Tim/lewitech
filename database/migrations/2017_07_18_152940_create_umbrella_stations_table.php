<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmbrellaStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umbrella_stations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('站点名称');
            $table->integer('amount')->nullable()->default(0)->comment('站点雨伞数量');
            $table->unsignedTinyInteger('status')->nullable()->comment('站点状态');
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
        Schema::dropIfExists('umbrella_stations');
    }
}
