<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'stories_categories';
    protected $guarded = [''];
}
