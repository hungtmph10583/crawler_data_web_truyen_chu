<?php

namespace App\Models\Crawler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Story;
use App\Models\Crawler\CrawlerAuthor;
use App\Models\Crawler\CrawlerChapter;
use App\Models\Crawler\CrawlerStoryCategory;
use App\Models\Crawler\CrawlerCategory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CrawlerStory extends Model
{
    use HasFactory;
    protected $table = 'crawler_stories';
    protected $guarded = [''];

    const STATUS_DEFAULT = 0;
    const STATUS_WAITING = 1;
    const STATUS_PROCESS = 2;
    const STATUS_COMPLETE = 3;
    const STATUS_ERROR = 4;

    public $status = [
        self::STATUS_DEFAULT => [
            'name' => 'Default',
            'class' => 'secondary'
        ],
        self::STATUS_WAITING => [
            'name' => 'Waiting',
            'class' => 'warning'
        ],
        self::STATUS_PROCESS => [
            'name' => 'Process',
            'class' => 'info'
        ],
        self::STATUS_COMPLETE => [
            'name' => 'Complete',
            'class' => 'success'
        ],
        self::STATUS_ERROR => [
            'name' => 'Error',
            'class' => 'danger'
        ]
    ];

    public $types = [
        self::STATUS_DEFAULT => [
            'name' => 'Notthing',
            'class' => 'light'
        ],
        self::STATUS_WAITING => [
            'name' => 'Waiting',
            'class' => 'warning'
        ],
        self::STATUS_PROCESS => [
            'name' => 'Process',
            'class' => 'info'
        ],
        self::STATUS_COMPLETE => [
            'name' => 'Complete',
            'class' => 'success'
        ],
        self::STATUS_ERROR => [
            'name' => 'Lỗi tổng số chapter',
            'class' => 'danger'
        ]
    ];

    public function getStatus()
    {
        return Arr::get($this->status, $this->s_status,[]);
    }

    public function getTypes()
    {
        return Arr::get($this->types, $this->s_type_id,[]);
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 's_story_id');
    }

    public function categories()
    {
        return $this->belongsToMany(CrawlerCategory::class, 'crawler_stories_categories', 'sc_story_id', 'sc_category_id');
    }

    public function storyCategory()
    {
        return $this->hasMany(CrawlerStoryCategory::class, 'sc_story_id');
    }

    public function crawlerAuthor()
    {
        return $this->belongsTo(CrawlerAuthor::class, 's_author_id');
    }

    public function crawlerChapter()
    {
        return $this->hasMany(CrawlerChapter::class, 'c_story_id');
    }

}
