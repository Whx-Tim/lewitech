<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumsToSignTimers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_timers', function (Blueprint $table) {
            $table->unsignedInteger('fail_count')->default(0)->after('apply_count')->comment('失败的人数');
            $table->double('reward', 20, 5)->default(0)->after('day')->comment('本轮奖金池');
            $table->timestamp('start_at')->before('created_at')->nullable()->comment('本轮开始时间');
            $table->timestamp('end_at')->before('created_at')->nullable()->comment('本轮结束时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_timers', function (Blueprint $table) {
            $table->dropColumn('fail_count');
            $table->dropColumn('reward');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }
}
