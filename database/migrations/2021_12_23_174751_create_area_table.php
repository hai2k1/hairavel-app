<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area', function (Blueprint $table) {
            $table->increments('area_id');
            $table->integer('code')->default(0)->comment('code');
            $table->integer('parent_code')->default(0)->index('parent_code')->comment('parent_code');
            $table->boolean('level')->default(0)->index('level')->comment('level');
            $table->char('name', 50)->nullable()->index('name')->comment('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area');
    }
}