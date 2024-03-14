<?php

namespace App\Http\Controllers\Crawler;

use App\Http\Controllers\Controller;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerChapter;
use Illuminate\Http\Request;

class CrawlerChapterController extends Controller
{
    public function index()
    {
        $chapters = CrawlerChapter::with('crawlerStory:id,s_name,s_slug')->paginate(100);
        $viewData = [
            'chapters' => $chapters,
        ];
        // dd($chapters);
        return view('crawler.chapter.index', $viewData);
    }
    public function show($s_slug, $c_slug)
    {
        $story = CrawlerStory::where('s_slug', $s_slug)->first();
        $chapters = CrawlerChapter::with('crawlerStory:id,s_slug')->where('c_story_id', $story->id)->get();
        $chapter = CrawlerChapter::with('crawlerStory:id,s_name')->where('c_story_id', $story->id)->where('c_slug', $c_slug)->first();
        $viewData = [
            'chapters' => $chapters,
            'chapter' => $chapter,
        ];
        return view('crawler.chapter.show', $viewData);
    }
}
