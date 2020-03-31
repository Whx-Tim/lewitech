<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('校企分店别名');
            $table->string('address')->nullable()->comment('校企分店地址');
            $table->string('phone')->nullable()->comment('校企分店电话');
            $table->string('linkman')->nullable()->comment('校企分店联系人');

            $table->unsignedInteger('business_id')->default(0)->index()->comment('校企外键id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_branches');
    }
}
