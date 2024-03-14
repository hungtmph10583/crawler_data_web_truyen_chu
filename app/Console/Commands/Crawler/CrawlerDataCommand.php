<?php

namespace App\Console\Commands\Crawler;

use App\Models\Crawler\CrawlerCategory;
use App\Models\Crawler\CrawlerType;
use App\Models\Crawler\CrawlerStory;
use App\Models\Category;
use App\Models\Type;
use App\Models\Story;
use App\Traits\HelpersCrawlerTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\HelpersClass\CliEcho;


class CrawlerDataCommand extends Command
{
    use HelpersCrawlerTrait;
    protected $signature = 'crawler:init';
    protected $description = 'Crawler';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->init();
    }

    protected function init(){
        // $this->crawlerCategory();
        $this->crawlerType();
    }

    /** Crawler Category Site
     * Website  : Trùm Truyện
     * url      : https://trumtruyen.vn/
     */
    protected function crawlerCategory(){
        $linkCrawler = 'https://trumtruyen.vn/';
        $this->info("|=> [ 1 ] >START: Crawler Category By URL< == == == == == == == == == == == == == == == == == == ==|");
        $this->warn("|-- -- -[1] Link: " . $linkCrawler);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($linkCrawler, false, $context);
        
        foreach ($html->find("#nav > div > div > ul > li > div > div > div > ul > li > a") as $link) {
            $nameCategory = $link->plaintext ?? "NULL";
            $linkCategory = $link->href ?? "NULL";
            $category = [
                'c_name'        => $nameCategory,
                'c_slug'        => Str::slug($nameCategory),
                'c_link'        => $linkCategory,
                'created_at'    => Carbon::now('Asia/Ho_Chi_Minh'),
                'c_domain'      => $linkCrawler,
            ];

            $this->warn("|-- -- -[1] Name: " . $nameCategory);
            // $this->warn("|-- -- -[1] -- -- --");
            $this->warn("|-- -- -[1] Href: " . $linkCategory);
            if (!$this->checkExistsCategory($nameCategory)) {
                // CrawlerCategory::insert($category);
                $categoryCrawler = CrawlerCategory::create($category);
                if ($categoryCrawler)
                {
                    $categoryData = Category::create($category);
                    if ($categoryData)
                    {
                        $categoryCrawler->c_category_id = $categoryData->id;
                        $categoryCrawler->save();
                    }
                }
                $this->info("|-- -- -[1] Insert Category Success");
            }else{
                $this->error("|-- -- -[1] Error Category Exists");
            }
        }
        
    }

    protected function getPageByCategory(){
        $url = $category->c_link;
        
    }

    /** Crawler Type(Danh sách) Site
     * Website  : metruyenhot
     * url      : https://trumtruyen.vn/
     */
    protected function crawlerType(){
        $linkContent = 'https://trumtruyen.vn/';
        $this->info("-- Type: ");
        $this->warn("-- Link: " . $linkContent);

        $html   = file_get_html($linkContent);

        foreach ($html->find('#nav > div.container > div.navbar-collapse.collapse > ul > li.dropdown > ul > li > a') as $key => $link) {
            $nameType = trim($link->plaintext ?? "NULL");
            $linkType = $link->href ?? "NULL";
            $type = [
                't_name'   => $nameType,
                't_slug'   => Str::slug($nameType),
                't_link'   => $linkType,
                't_domain'   => $linkContent,
                'created_at'   => Carbon::now('Asia/Ho_Chi_Minh'),
            ];

            $this->warn("-- -- -- Name: " . $nameType);
            if (!$this->checkExistsType($nameType)) {
                $this->info("-- -- -- Insert Type Success");
                // CrawlerType::insert($type);
                $typeCrawler = CrawlerType::create($type);
                if ($typeCrawler)
                {
                    $typeData = Type::create($type);
                    if ($typeData)
                    {
                        $typeCrawler->t_type_id = $typeData->id;
                        $typeCrawler->save();
                    }
                }
            }else{
                $this->error("-- -- -- Error Type Exists");
            }
        }
    }

    protected function getStarusTypeStories()
    {
        // $linkUrl = 'https://trumtruyen.vn/danh-sach/truyen-moi/';
        // $linkUrl = 'https://trumtruyen.vn/danh-sach/truyen-hot/';
        $linkUrl = 'https://trumtruyen.vn/danh-sach/truyen-full/';
        $this->warn("-- Link: " . $linkUrl);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($linkUrl, false, $context);
        
        foreach ($html->find("#list-page > div.col-truyen-main > div.list-main > div > div.col-list-info > div > h3 > a") as $link) {
            $nameStory  = $link->plaintext ?? "NULL";
            $getCrawlerStory   = $this->checkExistsStory($nameStory);
            if ($getCrawlerStory) {
                $getCrawlerStory->s_hot = 1;
                $getCrawlerStory->save();
                $this->success("|-- -- -[1] Successfully updated hot crawler story status");
                $story = Story::where('s_slug', Str::slug($nameStory))->first();
                if ($story) {
                    $story->s_hot = 1;
                    $story->save();
                }
                $this->success("|-- -- -[1] Successfully updated hot story status");
            }
            $this->danger("|-- -- -[1] Update hot story status failed");
        }
    }
}