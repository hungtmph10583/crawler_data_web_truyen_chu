<?php

namespace App\Console\Commands\Crawler;

use Carbon\Carbon;
use Goutte\Client;
use App\Models\Story;
use App\Models\Author;
use Illuminate\Support\Str;
use App\HelpersClass\CliEcho;
use App\Models\StoryCategory;
use Illuminate\Console\Command;
use App\Traits\HelpersCrawlerTrait;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerAuthor;
use App\Models\Crawler\CrawlerStoryCategory;

class CrawlerStoryTestCommand extends Command
{
    use HelpersCrawlerTrait;
    protected $signature = 'crawler:story-test';
    protected $description = 'Crawler Story Test';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->init();
    }

    protected function init()
    {
        $category = $this->getCategoryWaiting();
        // $category = $this->getCategoryProcess();
        if (!$category)
        {
            CliEcho::successnl("|=> [ 1 ] >Run Crawler Category Stop<");
            return;
        }
        $this->getStoryByCategory($category);
    }

    protected function getStoryByCategory($category)
    {
        CliEcho::infonl("|== == => [ 3 ] >START: Get Story By Category< == == == == == == == == == == == == == == == == == =|");

        for ($i=1; $i <= $category->c_total_page; $i++)
        {
            $linkContent = $category->c_link . '?page=' . $i;
            dd($linkContent);
            CliEcho::warningnl("|-- -- -- -- -[3] Link Category - Page: " . $linkContent);
            $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
            $html = file_get_html($linkContent, false, $context);
            
            foreach ($html->find("body > div.book-list > div > div > div.col-md-12.col-lg-9 > div.table-list > table > tbody > tr") as $key => $item)
            {
                $nameStory      = html_entity_decode($item->find("td.info > h3 > a",0)->plaintext ?? "NULL", ENT_QUOTES, "UTF-8");
                $linkDetail     = $item->find("td.info > h3 > a",0)->href ?? "NULL";
                $hotStory       = 0;
                $fullStory      = 0;
                if(!empty($item->find('span.label-title.label-hot', 0) ?? 0)) {
                    $hotStory = 1;
                }
                if(!empty($item->find('span.label-title.label-full', 0) ?? 0)) {
                    $fullStory = 3;
                }
                $data           = [
                    "s_name"            => $nameStory,
                    "s_slug"            => Str::slug($nameStory),
                    "s_link"            => $linkDetail,
                    "s_status"          => $fullStory,
                    "s_hot"             => $hotStory,
                    'created_at'        => Carbon::now('Asia/Ho_Chi_Minh')
                ];
                /** Lưu data truyện thuộc thể loại vào database nếu truyện chưa tồn tại trong data */
                if (!$this->checkExistsStory($nameStory)) {
                    $storyCrawler = CrawlerStory::create($data);
                    if ($storyCrawler) {
                        $storyData = Story::create($data);
                        if ($storyData) {
                            $storyCrawler->s_story_id = $storyData->id;
                            $storyCrawler->save();
                        }
                    }
                    CliEcho::successnl("|-- -- -- -- -[3] Insert Story ( " . $nameStory . " ) Success");
                    $this->getStoryDetail($category, $storyCrawler, $storyData);
                }else{
                    CliEcho::errornl("|-- -- -- -- -[3] Error Inserting Story ( " . $nameStory . " ) Because It Already Exists");
                }
                sleep(1);
            }
            sleep(1);

            $category->c_page_process = $i;
            $category->save();
        }

        CliEcho::infonl("|== == => [ 3 ] >END: Get Story By Category< == == == == == == == == == == == == == == == == == == |");
        CliEcho::infonl("");
    }

    /** ( 4 ) 
     * B1: Truy cập vào chi tiết từng chuyện
     *  * Update dữ liệu truyện
     *      - Thumbnail (s_thumbnail)
     *      - Description (s_description)
     *      - Số lượng page của truyện
    */
    protected function getStoryDetail($category, $storyCrawler, $storyData)
    {
        CliEcho::infonl("|== == == => [ 4 ] >START: Get Story Detail< == == == == == == == == == == == == == == == == == == |");
        $client = new Client();
        $link = $storyCrawler->s_link;
        $cut = $storyCrawler->s_name;
        CliEcho::warningnl("|-- -- -- -- -- -[4] Link Story Detail: " . $link);
        $crawler = $client->request('GET', $link);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = @file_get_html($link, false, $context);
        if($html==FALSE) {
            CliEcho::errornl('FALSE');
            if ($storyCrawler)
            {
                $storyCrawler->s_status = 4;
                $storyCrawler->s_type_id = 4;
                $storyCrawler->save();
            }
            CliEcho::errornl("|-- -- -- -- -- -[4] Error When Updating Crawler Story ( " . $storyCrawler->s_name . " ) Error");
        } else {
            $arrPageChapter = [];
            foreach ($html->find('#list-chapter > ul > li > a[title]') as $key => $item)
            {
                // CliEcho::successnl('Key: ' . $key);
                // CliEcho::successnl('plaintext: ' . $item->plaintext);
                // CliEcho::successnl('title: ' . $item->title);
                // CliEcho::successnl('href: ' . $item->href);
                // CliEcho::successnl('');
                $page = str_replace($cut, '', str_replace('- Trang', '', $item->title ?? 0));
                if (preg_match('/^[0-9]+$/', trim($page))) { $arrPageChapter[] = trim($page); }
                sleep(1);
            }

            if ($arrPageChapter) {
                $totalPageChapter = max($arrPageChapter);
            }else{
                $totalPageChapter = 1;
            }

            /** Start Save */
            $s_thumbnail            = $crawler->filter('#truyen > div > div > div > div.books > div > img')->eq(0)->attr('src') ?? null;
            $s_author_id            = $this->getNameAuthor($storyCrawler);
            $s_total_page_chapter   = $totalPageChapter;
            $s_total_chapter        = $this->getTotalPageChapter($storyCrawler, $s_total_page_chapter);
            $s_description          = $crawler->filter('#truyen > div > div > div > div.desc-text')->each(function ($node){ return $node->text(); })[0];
            $updated_at             = Carbon::now('Asia/Ho_Chi_Minh');

            if ($storyCrawler)
            {
                $storyCrawler->s_thumbnail          = $s_thumbnail;
                $storyCrawler->s_author_id          = $s_author_id;
                $storyCrawler->s_domain             = $category->c_domain;
                $storyCrawler->s_total_page_chapter = $s_total_page_chapter;
                $storyCrawler->s_total_chapter      = $s_total_chapter;
                $storyCrawler->s_description        = $s_description;
                $storyCrawler->updated_at           = $updated_at;
                $storyCrawler->save();
            }
            if ($storyData)
            {
                $storyData->s_thumbnail     = $s_thumbnail;
                $storyData->s_author_id     = $s_author_id;
                $storyData->s_total_chapter = $s_total_chapter;
                $storyData->s_description   = $s_description;
                $storyData->updated_at      = $updated_at;
                $storyData->save();
            }
            CliEcho::successnl("|-- -- -- -- -- -[4] Update Crawler Story ( " . $storyCrawler->s_name . " ) Success");
            /** End Save */
            sleep(1);
        }
        $this->getStoryCategories($storyCrawler);
        CliEcho::infonl("|== == == => [ 4 ] >END: Get Story Detail< == == == == == == == == == == == == == == == == == == = |");
    }

    /** ( _5 ) */
    protected function getNameAuthor($storyCrawler)
    {
        CliEcho::infonl("|== == == == => [ 5 ] >START: Get The Story Author's Name< == == == == == == == == == == == == == =|");
        $client = new Client();
        $link = $storyCrawler->s_link;
        $crawler = $client->request('GET', $link);
        
        $nameAuthor = $crawler->filter('#truyen > div.col-xs-12.col-sm-12.col-md-9.col-truyen-main > div.col-xs-12.col-info-desc > div.col-xs-12.col-sm-4.col-md-4.info-holder > div.info > div:nth-child(1) > h3 > a')->each(function ($node) {
            return $node->text();
        })[0];
        
        $data       = [
            "a_name"       => $nameAuthor,
            "a_slug"       => Str::slug($nameAuthor),
            'created_at'   => Carbon::now('Asia/Ho_Chi_Minh')
        ];
        
        if (!$this->checkExistsAuthor($nameAuthor))
        {
            $authorCrawler = CrawlerAuthor::create($data);
            if ($authorCrawler)
            {
                $storyData = Author::create($data);
                if ($storyData)
                {
                    $authorCrawler->a_author_id = $storyData->id;
                    $authorCrawler->save();
                }
            }
            CliEcho::successnl("|-- -- -- -- -- -- -[5] Insert Author ( " . $nameAuthor . " ) Success");
        }else{
            $authorCrawler  = CrawlerAuthor::where('a_slug', Str::slug($nameAuthor))->first();
            CliEcho::errornl("|-- -- -- -- -- -- -[5] Error Inserting Author ( " . $nameAuthor . " ) Because It Already Exists");
        }
        CliEcho::infonl("|== == == == => [ 5 ] >END: Get The Story Author's Name< == == == == == == == == == == == == == == |");
        return $authorCrawler->id;
    }

    /** ( _6 ) */
    protected function getTotalPageChapter($storyCrawler, $s_total_page_chapter)
    {
        CliEcho::infonl("|== == == == == => [ 6 ] >START: Get Total Page Chapter< == == == == == == == == == == == == == == |");
        $link = $storyCrawler->s_link . 'trang-' . $s_total_page_chapter;

        CliEcho::warning("|-- -- -- -- -- -- -- -[6] Link getTotalPageChapter: " . $link);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($link, false, $context);
        /** */
        $count = 0;
        foreach ($html->find('#list-chapter > div.row > div > ul > li > a') as $key => $item)
        {
            $count = ++$key;
        }
        if ($s_total_page_chapter > 1) {
            $result = 50 * ($s_total_page_chapter - 1) + $count;
        }else{
            $result = $count;
        }
        // dump($count);
        // dump($result);
        /** */
        CliEcho::infonl("|== == == == == => [ 6 ] >END: Get Total Page Chapter< == == == == == == == == == == == == == == ==|");
        return $result;
    }

    /** ( _7 ) */
    protected function getStoryCategories($storyCrawler){
        CliEcho::infonl("|== == == == == == => [ 7 ] >START: Insert Data Categories Of The Story< == == == == == == == == ==|");
        $link = $storyCrawler->s_link;
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($link, false, $context);
        foreach ($html->find('#truyen > div.col-truyen-main > div.col-info-desc > div.info-holder > div.info > div:nth-child(2) > h3 > a[itemprop=genre]') as $key => $item)
        {
            $categoryName   = $item->plaintext ?? '';
            $checkExists    = $this->getCategoriesStory($categoryName);
                $data = [
                    'sc_story_id'       => $storyCrawler->id,
                    'sc_category_id'    => $checkExists->id
                ];
            if ($checkExists) {
                $categoriesStory = CrawlerStoryCategory::create($data);
                CliEcho::successnl("|-- -- -- -- -- -- -- -- -[7] >Insert Data CrawlerCategories Of The CrawlerStory Success<");
                if ($categoriesStory)
                {
                    StoryCategory::create($data);
                    CliEcho::successnl("|-- -- -- -- -- -- -- -- -[7] >Insert Data Categories Of The Story Success<");
                }
            } else {
                CliEcho::warningnl("|-- -- -- -- -- -- -- -- -[7] >ERROR< Insert Data CrawlerCategories Of The CrawlerStory >FALSE<");
            }
            sleep(1);
        }
        CliEcho::infonl("|== == == == == == => [ 7 ] >END: Insert Data Categories Of The Story< == == == == == == == == == =|");
    }
}
