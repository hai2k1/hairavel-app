<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorViewsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_views_data', function (Blueprint $table) {
            $table->increments('data_id');
            $table->string('has_type', 255)->nullable()->index('has_type')->comment('association type');
            $table->integer('has_id')->default(0)->index('has_id')->comment('association id');
            $table->char('driver', 10)->nullable()->index('driver')->comment('device');
            $table->char('date', 8)->nullable()->index('date')->comment('date');
            $table->integer('pv')->default(1)->comment('pageviews');
            $table->integer('uv')->default(0)->comment('Visitors');
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
        Schema::dropIfExists('visitor_views_data');
    }
}