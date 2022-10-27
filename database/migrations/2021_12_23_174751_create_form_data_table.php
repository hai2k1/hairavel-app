<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_data', function (Blueprint $table) {
            $table->increments('data_id');
            $table->integer('form_id')->default(0)->index('form_id')->comment('form_id');
            $table->string('has_type', 255)->nullable()->comment('association type');
            $table->integer('has_id')->default(0)->index('has_id')->comment('association id');
            $table->longText('data')->nullable()->comment('form content');
            $table->boolean('status')->default(1)->comment('status');
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
        Schema::dropIfExists('form_data');
    }
}
