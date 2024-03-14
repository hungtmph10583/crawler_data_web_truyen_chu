<?php

namespace App\Models\Crawler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;
use Illuminate\Support\Arr;

class CrawlerType extends Model
{
    use HasFactory;
    protected $table = 'crawler_types';
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
        return Arr::get($this->status, $this->t_status,[]);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 't_type_id');
    }
}
