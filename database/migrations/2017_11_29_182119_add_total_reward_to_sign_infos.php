<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalRewardToSignInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_infos', function (Blueprint $table) {
            $table->double('total_reward', 20, 2)->nullable()->default(0)->comment('累计获取奖金');
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
            $table->dropColumn('total_reward');
        });
    }
}
