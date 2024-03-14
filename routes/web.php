<?php

use Illuminate\Support\Facades\Route;
// use App\Controllers\Frontend\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Crawler\CrawlerCategoryController@index');

Route::group(['prefix' => 'crawler', 'namespace' => 'Crawler'], function(){
    Route::get('', function(){
        dd("ok");
    });
    Route::group(['prefix' => 'category'], function(){
        Route::get('', 'CrawlerCategoryController@index')->name('get_crawler.category.index');
    });
    Route::group(['prefix' => 'type'], function(){
        Route::get('', 'CrawlerTypeController@index')->name('get_crawler.type.index');
    });
    Route::group(['prefix' => 'story'], function(){
        Route::get('', 'CrawlerStoryController@index')->name('get_crawler.story.index');
        Route::get('{story}', 'CrawlerStoryController@show')->name('get_crawler.story.show');
    });
    Route::group(['prefix' => 'chapter'], function(){
        Route::get('', 'CrawlerChapterController@index')->name('get_crawler.chapter.index');
        Route::get('{story}/{chapter}', 'CrawlerChapterController@show')->name('get_crawler.chapter.show');
    });
});
