<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form', function (Blueprint $table) {
            $table->increments('form_id');
            $table->char('name', 100)->nullable()->comment('form name');
            $table->string('description', 255)->nullable()->comment('form description');
            $table->char('menu', 50)->nullable()->comment('menu');
            $table->longText('data')->nullable()->comment('form configuration');
            $table->boolean('manage')->nullable()->default(0)->comment('independent management');
            $table->char('search', 50)->nullable()->comment('search field');
            $table->boolean('audit')->nullable()->default(0)->comment('audit status');
            $table->boolean('submit')->nullable()->default(0)->comment('external submission');
            $table->string('tpl_list', 255)->nullable()->comment('list template');
            $table->string('tpl_info', 255)->nullable()->comment('Detail template');
            $table->integer('interval')->nullable()->default(10)->comment('search');
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
        Schema::dropIfExists('form');
    }
}
