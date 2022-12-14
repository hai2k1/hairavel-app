<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_views', function (Blueprint $table) {
            $table->increments('view_id');
            $table->string('has_type', 255)->nullable()->index('has_type')->comment('association type');
            $table->integer('has_id')->default(0)->index('has_id')->comment('association id');
            $table->integer('pv')->default(0)->comment('pageviews');
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
        Schema::dropIfExists('visitor_views');
    }
}
