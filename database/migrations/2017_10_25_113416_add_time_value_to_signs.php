<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeValueToSigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signs', function (Blueprint $table) {
            $table->unsignedInteger('time_value')->default(0)->nullable()->comment('今日签到早起值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signs', function (Blueprint $table) {
            $table->dropColumn('time_value');
        });
    }
}
