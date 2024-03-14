<?php

namespace App\Http\Controllers\Crawler;

use App\Http\Controllers\Controller;
use App\Models\Crawler\CrawlerType;
use Illuminate\Http\Request;

class CrawlerTypeController extends Controller
{
    public function index()
    {
        $types = CrawlerType::with('type:id,t_name')->orderByDesc('id')->paginate(100);
        $viewData = [
            'types' => $types
        ];
        return view('crawler.type.index', $viewData);
    }
}
