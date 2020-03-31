<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToSignShares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_shares', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->nullable()->comment('帮助补签类型');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_shares', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
