<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToSmsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_histroys', function (Blueprint $table) {
            $table->string('code')->nullable()->comment('短信返回状态码');
            $table->string('request_id')->nullable()->comment('短信请求ID');
            $table->string('biz_id')->nullable()->comment('短信回执id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_histroys', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('request_id');
            $table->dropColumn('biz_id');
        });
    }
}
