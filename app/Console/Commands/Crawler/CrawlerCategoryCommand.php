<?php

namespace App\Console\Commands\Crawler;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Story;
use App\Models\Category;
use Illuminate\Support\Str;
use App\HelpersClass\CliEcho;
use Illuminate\Console\Command;
use App\Models\Crawler\CrawlerType;
use App\Traits\HelpersCrawlerTrait;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerCategory;

class CrawlerCategoryCommand extends Command
{

    use HelpersCrawlerTrait;
    protected $signature = 'crawler:category';
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
        $this->crawlerCategory();
        // $this->getPageCategory();
    }

    /** Crawler Category Site: ghientruyenchu
     * url      : https://ghientruyenchu.com/
     */
    protected function crawlerCategory(){
        $linkCrawler = 'https://ghientruyenchu.com';
        CliEcho::infonl("|=> [ 1 ] >START: Crawler Category By URL< == == == == == == == == == == == == == == == == == == ==|");
        CliEcho::warningnl("|-- -- -[1] Link: " . $linkCrawler);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($linkCrawler, false, $context);
        
        foreach ($html->find("body > div.section-header.pc > div > div > div.col-xs-12.col-md-7 > ul > li.list > div > ul > li > a") as $link) {
            $nameCategory = $link->plaintext ?? "NULL";
            $linkCategory = $link->href ?? "NULL";
            CliEcho::warningnl("|-- -- -[1] Href: " . $linkCategory);
            $category = [
                'c_name'        => trim($nameCategory),
                'c_slug'        => Str::slug($nameCategory),
                'c_link'        => $linkCrawler . trim($linkCategory),
                'c_total_page'  => $this->getPageCategory($linkCategory, $nameCategory),
                'created_at'    => Carbon::now('Asia/Ho_Chi_Minh'),
                'c_domain'      => $linkCrawler,
            ];

            CliEcho::successnl("|-- -- -[1] Name: " . $nameCategory);
            CliEcho::warningnl("|-- -- -[1] Href: " . $linkCategory);
            if (!$this->checkExistsCategory($nameCategory)) {
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
                CliEcho::infonl("|-- -- -[1] Insert Category " . trim($nameCategory) . " Success");
            }else{
                CliEcho::errornl("|-- -- -[1] Error Category " . trim($nameCategory) . " Exists");
            }
        }
        CliEcho::infonl("|=> [ 1 ] >END: Crawler Category By URL< == == == == == == == == == == == == == == == == == == == |");
    }

    protected function getPageCategory($linkCategory, $nameCategory)
    {
        $link = 'https://ghientruyenchu.com' . $linkCategory;
        CliEcho::infonl("");
        CliEcho::infonl("|== => [ 2 ] >START: Get Page By Category< == == == == == == == == == == == == == == == == == == ==|");
        CliEcho::warningnl("|-- -- - --[2] Link Category: " . $link);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($link, false, $context);

        $arrPageCategory = [];
        $cut = 'https://ghientruyenchu.com/the-loai/' . Str::slug($nameCategory);
        foreach ($html->find('body > div.book-list > div > div > div > div.table-list > div > div > div > div > ul > li > a') as $item)
        {
            $page = str_replace($cut, '', str_replace('?page=', '', $item->href ?? 0));
            if (preg_match('/^[0-9]+$/', trim($page))) { $arrPageCategory[] = trim($page); }
        }
        if ($arrPageCategory) { $totalPageCategory = max($arrPageCategory); }else{ $totalPageCategory = 1; }

        // CliEcho::errornl('page: ' . $totalPageCategory);
        // CliEcho::successnl("|-- -- - --[2] The Total Pages Of Category's " . $nameCategory . ' is: ' . $totalPageCategory);
        CliEcho::infonl("|== => [ 2 ] >END: Get Page By Category< == == == == == == == == == == == == == == == == == == == =|");
        CliEcho::infonl("");
        return $totalPageCategory;
    }
}
