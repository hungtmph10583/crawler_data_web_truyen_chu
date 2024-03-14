<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('c_name')->nullable();
            $table->string('c_slug')->nullable()->unique();
            $table->string('c_description', 300)->nullable();
            $table->string('c_thumbnail')->nullable();
            $table->tinyInteger('c_status')->default(0);
            $table->tinyInteger('c_new')->default(0);
            $table->tinyInteger('c_hot')->default(0);
            $table->tinyInteger('c_sort')->default(0);
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
        Schema::dropIfExists('categories');
    }
}
