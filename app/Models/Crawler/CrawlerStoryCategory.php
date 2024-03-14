<?php

namespace App\Models\Crawler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlerStoryCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'crawler_stories_categories';
    protected $guarded = [''];

    public function category(){
        return $this->belongsTo(Role::class, 'sc_category_id');
    }

    public function story(){
        return $this->belongsTo(Role::class, 'sc_story_id');
    }
}
