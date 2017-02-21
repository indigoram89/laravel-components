<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentComponentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laravel_component_component', function (Blueprint $table) {
            $table->integer('parent_component_id')->unsigned();
            $table->integer('child_component_id')->unsigned();

            $table->foreign('parent_component_id')->references('id')->on('laravel_components')->onDelete('cascade');
            $table->foreign('child_component_id')->references('id')->on('laravel_components')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laravel_component_component');
    }
}
