<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeValueColumnToSignMedalWeeks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_medal_weeks', function (Blueprint $table) {
            $table->unsignedInteger('time_value')->after('rank')->default(0)->nullable()->comment('每周的平均早起值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_medal_weeks', function (Blueprint $table) {
            $table->dropColumn('time_value');
        });
    }
}
