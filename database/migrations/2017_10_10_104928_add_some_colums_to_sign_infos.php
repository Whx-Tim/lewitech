<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumsToSignInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_infos', function (Blueprint $table) {
            $table->double('reward', 20, 5)->default(0)->after('duration_count')->comment('个人奖金池');
            $table->unsignedInteger('time_value')->default(0)->after('duration_count')->comment('早起值');
            $table->unsignedInteger('month_count')->default(0)->nullable()->after('total_count')->comment('月签到次数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_infos', function (Blueprint $table) {
            $table->dropColumn('reward');
            $table->dropColumn('time_value');
            $table->dropColumn('month_count');
        });
    }
}
