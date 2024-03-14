<?php

namespace App\Http\Controllers\Crawler;

use App\Http\Controllers\Controller;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerChapter;
use App\Models\Crawler\CrawlerAuthor;
use App\Models\Crawler\CrawlerCategory;
use App\Models\Crawler\CrawlerStoryCategory;
use Illuminate\Http\Request;

class CrawlerStoryController extends Controller
{
    public function index()
    {
        // $stories = CrawlerStory::with('story:id,s_name', 'crawlerAuthor:id,a_name', 'categories:id,c_name')->orderByDesc('id')->paginate(100);
        $stories = CrawlerStory::with('story:id,s_name', 'crawlerAuthor:id,a_name', 'categories:id,c_name')->paginate(100);
        $viewData = [
            'stories' => $stories,
        ];
        return view('crawler.story.index', $viewData);
    }
    public function show($s_slug)
    {
        $story = CrawlerStory::where('s_slug', $s_slug)->first();
        $chapters = CrawlerChapter::where('c_story_id', $story->id)->get();
        $viewData = [
            'chapters' => $chapters,
        ];
        return view('crawler.chapter.index', $viewData);
    }
}
