<?php

namespace App\Models\Crawler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Author;

class CrawlerAuthor extends Model
{
    use HasFactory;
    protected $table = 'crawler_authors';
    protected $guarded = [''];

    public function crawlerStory()
    {
        return $this->hasMany(CrawlerStory::class, 's_author_id');
    }
}
