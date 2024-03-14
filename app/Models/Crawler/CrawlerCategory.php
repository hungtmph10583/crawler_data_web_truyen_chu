<?php

namespace App\Models\Crawler;

use App\Models\Story;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CrawlerCategory extends Model
{
    use HasFactory;
    protected $table = 'crawler_categories';
    protected $guarded = [''];

    const STATUS_DEFAULT    = 0;
    const STATUS_WAITING    = 1;
    const STATUS_PROCESS    = 2;
    const STATUS_COMPLETE   = 3;
    const STATUS_CHECK      = 4;

    public $status = [
        self::STATUS_DEFAULT => [
            'name' => 'Default',
            'class' => 'secondary'
        ],
        self::STATUS_WAITING => [
            'name' => 'Crawler Waiting',
            'class' => 'warning'
        ],
        self::STATUS_PROCESS => [
            'name' => 'Processing Crawler',
            'class' => 'info'
        ],
        self::STATUS_COMPLETE => [
            'name' => 'Crawler Complete',
            'class' => 'success'
        ],
        self::STATUS_CHECK => [
            'name' => 'Crawler Error',
            'class' => 'danger'
        ]
    ];

    public function getStatus()
    {
        return Arr::get($this->status, $this->c_status,[]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'c_category_id');
    }

    public function stories()
    {
        return $this->belongsToMany(CrawlerStory::class, 'crawler_stories_categories', 'sc_category_id', 'sc_story_id');
    }

    public function storyCategory()
    {
        return $this->hasMany(CrawlerStoryCategory::class, 'sc_category_id');
    }
}
