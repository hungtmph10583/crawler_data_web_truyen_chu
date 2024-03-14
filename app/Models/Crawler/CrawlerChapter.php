<?php

namespace App\Models\Crawler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Crawler\CrawlerStory;

class CrawlerChapter extends Model
{
    use HasFactory;
    protected $table = 'crawler_chapters';
    protected $guarded = [''];

    public function crawlerStory()
    {
        return $this->belongsTo(CrawlerStory::class, 'c_story_id');
    }
}
