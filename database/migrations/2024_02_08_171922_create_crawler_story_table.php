<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrawlerStoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_categories', function (Blueprint $table) {
            $table->id();
            $table->string('c_name')->nullable();
            $table->string('c_slug')->nullable()->unique();
            $table->string('c_description', 300)->nullable();
            $table->string('c_thumbnail')->nullable();
            $table->string('c_link')->nullable();
            $table->integer('c_category_id')->default(0);
            $table->string('c_domain')->nullable();
            $table->tinyInteger('c_status')->default(0);
            $table->tinyInteger('c_hot')->default(0);
            $table->tinyInteger('c_sort')->default(0);
            $table->integer('c_total_page')->default(0);
            $table->integer('c_page_process')->default(1);
            $table->timestamps();
        });
        Schema::create('crawler_types', function (Blueprint $table) {
            $table->id();
            $table->string('t_name')->nullable();
            $table->string('t_slug')->nullable();
            $table->string('t_description', 300)->nullable();
            $table->string('t_link')->nullable();
            $table->integer('t_type_id')->default(0);
            $table->string('t_domain')->nullable();
            $table->tinyInteger('t_hot')->default(0);
            $table->tinyInteger('t_sort')->default(0);
            $table->timestamps();
        });
        Schema::create('crawler_stories', function (Blueprint $table) {
            $table->id();
            $table->string('s_name')->nullable();
            $table->string('s_slug')->nullable()->unique();
            $table->text('s_thumbnail')->nullable();
            $table->integer('s_type_id')->default(0);
            $table->integer('s_author_id')->default(0);
            $table->string('s_link')->nullable();
            $table->string('s_domain')->nullable();
            $table->integer('s_total_page_chapter')->default(0);
            $table->integer('s_story_id')->default(0);
            $table->integer('s_total_chapter')->default(0);
            $table->tinyInteger('s_status')->default(0);
            $table->text('s_description')->nullable();
            $table->integer('s_view')->default(0);
            $table->integer('s_favourite')->default(0);
            $table->integer('vote_number')->default(0);
            $table->integer('vote_total')->default(0);
            $table->tinyInteger('s_full')->default(0);
            $table->tinyInteger('s_new')->default(0);
            $table->tinyInteger('s_hot')->default(0);
            $table->timestamps();
        });
        Schema::create('crawler_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('c_name')->nullable();
            $table->string('c_slug')->nullable();
            $table->string('c_link_chapter')->nullable();
            $table->integer('c_story_id')->default(0);
            $table->longText('c_content')->nullable();
            $table->timestamps();
        });
        Schema::create('crawler_authors', function (Blueprint $table) {
            $table->id();
            $table->string('a_name')->nullable();
            $table->string('a_slug')->nullable()->unique();
            $table->integer('a_author_id')->default(0);
            $table->tinyInteger('a_status')->default(0);
            $table->timestamps();
        });
        Schema::create('crawler_stories_categories', function (Blueprint $table) {
            $table->integer('sc_story_id')->default(0);
            $table->integer('sc_category_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crawler_categories');
        Schema::dropIfExists('crawler_types');
        Schema::dropIfExists('crawler_stories');
        Schema::dropIfExists('crawler_chapters');
        Schema::dropIfExists('crawler_authors');
        Schema::dropIfExists('crawler_stories_categories');
    }
}
