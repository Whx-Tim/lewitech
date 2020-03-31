<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemindColumnToSignDonates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_donates', function (Blueprint $table) {
            $table->unsignedTinyInteger('remind')->nullable()->default(0)->after('type')->comment('消息提醒');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_donates', function (Blueprint $table) {
            $table->dropColumn('remind');
        });
    }
}
