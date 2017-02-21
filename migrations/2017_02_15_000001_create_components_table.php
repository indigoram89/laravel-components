<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laravel_components', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('key')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('component_id')->unsigned()->nullable();

            $table->boolean('visible')->default(true);

            $table->unique('key');
            $table->foreign('parent_id')->references('id')->on('laravel_components')->onDelete('cascade');
            $table->foreign('component_id')->references('id')->on('laravel_components')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laravel_components');
    }
}
