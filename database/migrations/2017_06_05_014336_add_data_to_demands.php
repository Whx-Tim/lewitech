<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToDemands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shareholder_demands', function (Blueprint $table) {
            $table->longText('data')->nullable()->comment('需求发布额外信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shareholder_demands', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
}
