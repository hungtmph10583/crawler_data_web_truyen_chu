<?php

namespace App\Http\Controllers\Crawler;

use App\Http\Controllers\Controller;
use App\Models\Crawler\CrawlerCategory;
use Illuminate\Http\Request;

class CrawlerCategoryController extends Controller
{
    public function index()
    {
        $categories = CrawlerCategory::with('category:id,c_name')->orderByDesc('id')->paginate(100);
        $viewData = [
            'categories' => $categories
        ];
        return view('crawler.category.index', $viewData);
    }
}
