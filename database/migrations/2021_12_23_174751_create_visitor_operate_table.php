<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorOperateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_operate', function (Blueprint $table) {
            $table->char('uuid', 36)->unique('uuid')->comment('uuid');
            $table->char('has_type', 50)->nullable()->index('has_type')->comment('association type');
            $table->integer('has_id')->default(0)->index('has_id')->comment('association id');
            $table->string('username', 100)->nullable()->index('username')->comment('username');
            $table->char('method', 6)->nullable()->index('method')->comment('action');
            $table->string('route', 255)->nullable()->index('route')->comment('route');
            $table->char('name', 50)->nullable()->index('name')->comment('name');
            $table->char('desc', 50)->nullable()->comment('description');
            $table->text('params')->nullable()->comment('params');
            $table->string('ip', 45)->nullable()->comment('ip');
            $table->string('ua', 255)->nullable()->comment('ua');
            $table->char('browser', 50)->nullable()->comment('browser');
            $table->char('device', 50)->nullable()->comment('device');
            $table->boolean('mobile')->default(0)->comment('mobile');
            $table->char('time', 30)->default('0')->comment('Record time');
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
        Schema::dropIfExists('visitor_operate');
    }
}