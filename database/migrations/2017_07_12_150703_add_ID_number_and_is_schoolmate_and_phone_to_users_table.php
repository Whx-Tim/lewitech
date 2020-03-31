<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIDNumberAndIsSchoolmateAndPhoneToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ID_number')->unique()->nullable()->comment('身份证号码')->after('openid');
            $table->string('phone')->unique()->nullable()->comment('手机号码')->after('openid');
            $table->tinyInteger('is_schoolmate')->nullable()->comment('校友认证状态')->after('openid');
            $table->tinyInteger('is_real')->nullable()->comment('实名认证状态')->after('openid');
            $table->string('real_name')->nullable()->comment('真实姓名')->after('openid');
            $table->string('birthday')->nullable()->comment('生日')->after('openid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ID_number');
            $table->dropColumn('phone');
            $table->dropColumn('is_schoolmate');
            $table->dropColumn('is_real');
            $table->dropColumn('real_name');
            $table->dropColumn('birthday');
        });
    }
}
