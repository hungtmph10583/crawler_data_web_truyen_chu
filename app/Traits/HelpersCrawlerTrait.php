<?php
/** Created by PhpStorm . ...*/

namespace App\Traits;


use App\Models\Crawler\CrawlerCategory;
use App\Models\Category;
use App\Models\Crawler\CrawlerType;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerChapter;
use App\Models\Crawler\CrawlerAuthor;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait HelpersCrawlerTrait
{
     /**
     * @param $name
     * @return bool
     */
    protected function checkExistsCategory($name)
    {
        $slug       = Str::slug($name);
        $category   = CrawlerCategory::where('c_slug', $slug)->first();
        if ($category){
            return true;
        }else{
            return false;
        }
    }

    protected function checkExistsType($name)
    {
        $slug       = Str::slug($name);
        $type       = CrawlerType::where('t_slug', $slug)->first();
        if ($type) {
            return true;
        }else{
            return false;
        }
    }

    protected function checkExistsStory($name)
    {
        $slug       = Str::slug($name);
        $story      = CrawlerStory::where('s_slug', $slug)->first();
        if ($story){
            return true;
        }else{
            return false;
        }
    }

    protected function checkExistsAuthor($name)
    {
        $slug       = Str::slug($name);
        $author      = CrawlerAuthor::where('a_slug', $slug)->first();
        if ($author){
            return true;
        }else{
            return false;
        }
    }

    protected function checkExistsChapter($slug, $story)
    {
        /**
         * Check chapter crawler đã tồn tại hay chưa
         * Nếu đã tồn tại thì trả về true (ko lưu nữa, break luôn)
         * Nếu chưa tồn tại thì trả về false (lưu dữ liệu)
         */
        $chapter    = CrawlerChapter::where('c_slug', $slug)->where('c_story_id', $story->id)->first();

        if ($chapter){ return true; }else{ return false; }
    }

    protected function getCategoriesStory($name)
    {
        $slug       = Str::slug($name);
        $category   = CrawlerCategory::where('c_slug', $slug)->first();
        if ($category){
            return $category;
        }else{
            $insertCategory = [
                'c_name'        => $name,
                'c_slug'        => $slug,
                'c_status'      => 4,
                'created_at'    => Carbon::now('Asia/Ho_Chi_Minh'),
            ];
            $categoryCrawler = CrawlerCategory::create($insertCategory);
                if ($categoryCrawler)
                {
                    $categoryData = Category::create($insertCategory);
                    if ($categoryData)
                    {
                        $categoryCrawler->c_category_id = $categoryData->id;
                        $categoryCrawler->save();
                    }
                }
            return $categoryCrawler;
        }
    }

    /**
     * @return false
     * Lấy category chưa crawler
     */
    protected function getCategoryDefault()
    {
        $category = CrawlerCategory::where('c_status',0)->first();
        if ($category)
        {
            $category->c_status = CrawlerCategory::STATUS_WAITING;
            $category->save();
            return $category;
        }
        
        return false;
    }

    protected function getCategoryWaiting()
    {
        $category = CrawlerCategory::where('c_status',1)->first();
        if ($category)
        {
            $category->c_status = CrawlerCategory::STATUS_PROCESS;
            $category->save();
            return $category;
        }
        
        return false;
    }

    protected function getCategoryProcess()
    {
        $category = CrawlerCategory::where('c_status',2)->first();
        if ($category)
        {
            $category->c_status = CrawlerCategory::STATUS_COMPLETE;
            $category->save();
            return $category;
        }
        
        return false;
    }

    /**
     * @return false
     * Lấy story status
     */
    protected function getStoryDefault()
    {
        $story = CrawlerStory::where('s_status',0)->first();
        if ($story)
        {
            // $story->s_status = CrawlerStory::STATUS_WAITING;
            // $story->save();
            return $story;
        }
        
        return false;
    }

    protected function getStoryWaiting()
    {
        $story = CrawlerStory::where('s_status',1)->first();
        if ($story)
        {
            $story->s_status = CrawlerStory::STATUS_PROCESS;
            $story->save();
            return $story;
        }
        
        return false;
    }
}