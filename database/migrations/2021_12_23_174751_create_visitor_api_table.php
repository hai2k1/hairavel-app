<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_api', function (Blueprint $table) {
            $table->integer('api_id', true);
            $table->char('method', 6)->nullable()->index('method')->comment('action');
            $table->char('name', 50)->nullable()->index('name')->comment('name');
            $table->char('desc', 50)->nullable()->index('date')->comment('description');
            $table->integer('date')->nullable()->default(0)->index('desc')->comment('date');
            $table->integer('pv')->nullable()->default(1)->comment('visits');
            $table->integer('uv')->nullable()->default(0)->comment('Visitors');
            $table->char('min_time', 30)->nullable()->comment('minimum response');
            $table->char('max_time', 30)->nullable()->comment('max_time');
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
        Schema::dropIfExists('visitor_api');
    }
}