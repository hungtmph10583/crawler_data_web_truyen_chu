<?php

namespace App\Console\Commands\Crawler;

use Goutte\Client;
use App\HelpersClass\CliEcho;
use Illuminate\Console\Command;
use App\Traits\HelpersCrawlerTrait;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Crawler\CrawlerStory;
use App\Models\Crawler\CrawlerAuthor;
use App\Models\Crawler\CrawlerStoryCategory;
use App\Models\Story;
use App\Models\Author;
use App\Models\StoryCategory;

class CrawlerStoryCommand extends Command
{
    use HelpersCrawlerTrait;
    protected $signature = 'crawler:story';
    protected $description = 'Command story';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->init();
    }

    public function init()
    {
        // $category = $this->getCategoryDefault();
        $category = $this->getCategoryProcess();
        if (!$category)
        {
            CliEcho::successnl("|=> [ 1 ] >Run Crawler Category Stop<");
            return;
        }
        /** 1: get page category */
        // $this->getPageByCategory($category);
        $this->getStoryByCategory($category);
    }

    /** ( 2 ) Lấy số lượng trang (chứa ndung truyện) của thể loại
     * Cập nhật số lượng trang của thể loại vào Table CrawlerCategories
     */
    protected function getPageByCategory($category)
    {
        $link = $category->c_link;
        CliEcho::infonl("|== => [ 2 ] >START: Get Page By Category< == == == == == == == == == == == == == == == == == == ==|");
        CliEcho::warningnl("|-- -- - --[2] Link Category: " . $category->c_link);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($link, false, $context);
        $lastPage = 1;
        foreach ($html->find('#wrap > div > div > ul > li> a') as $item)
        {
            $page = trim($item->plaintext) ?? '';
            $arr = "qwertyuiopasdfghjklzxcvbnm/:.-";
            
            if (preg_match("/^[0-9]+$/", $page) || $page == 'Cuối &raquo;')
            {
                $lastPage = str_replace(str_split($arr), '', $item->href ?? '');
            }
            // sleep(1);
        }
        CliEcho::successnl("|-- -- - --[2] The Total Pages Of Category's " . $category->c_name . ' is: ' . $lastPage);
        $category->c_total_page = $lastPage;/**2. Update total page by category */
        $category->save();
        CliEcho::infonl("|== => [ 2 ] >END: Get Page By Category< == == == == == == == == == == == == == == == == == == == =|");
        $this->init();
    }

    /** ( 3 )
     * Lấy dữ liệu truyện qua Category bằng cách:
     * B1: truy cập vào từng page thể loại qua (c_total_page)
     * Lưu dữ liệu CrawlerStory (Nếu dữ liệu chưa tồn tại)
     *      - Tên truyện (s_name)
     *      - Link chi tiết truyện (s_description)
     *      - Trạng thái HOT (s_hot)
     *      - Trạng thái truyện Full hay chưa (s_status)
     * * Check lưu thành công CrawlerStory thì lưu vào bảng chính Story
     *      - Tên truyện (s_name)
     *      - Link chi tiết truyện (s_description)
     *      - Trạng thái HOT (s_hot)
     *      - Trạng thái truyện Full hay chưa (s_status)
     * * * Vào getStoryDetail nếu lưu thành công CrawlerStory & Story
     *  */
    protected function getStoryByCategory($category)
    {
        CliEcho::infonl("|== == => [ 3 ] >START: Get Story By Category< == == == == == == == == == == == == == == == == == =|");
        for ($i=1; $i <= $category->c_total_page; $i++)
        {
            $linkContent = $category->c_link . 'trang-' . $i;
            CliEcho::warningnl("|-- -- -- -- -[3] Link Category - Page: " . $linkContent);
            $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
            $html = file_get_html($linkContent, false, $context);
            foreach ($html->find("#list-page > div > div.list-truyen > div > div.col-list-info > div") as $key => $item)
            {
                $nameStory      = html_entity_decode($item->find("h3 > a",0)->plaintext ?? "NULL", ENT_QUOTES, "UTF-8");
                $linkDetail     = $item->find("h3 > a",0)->href ?? "NULL";
                $hotStory       = 0;
                $fullStory      = 0;
                if(!empty($item->find('span.label-title.label-hot', 0) ?? 0)) { $hotStory = 1; }
                if(!empty($item->find('span.label-title.label-full', 0) ?? 0)) { $fullStory = 3; }
                $data           = [
                    "s_name"    => $nameStory,
                    "s_slug"    => Str::slug($nameStory),
                    "s_link"    => $linkDetail,
                    "s_status"  => $fullStory,
                    "s_hot"     => $hotStory,
                    'created_at'=> Carbon::now('Asia/Ho_Chi_Minh')
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
            $category->c_page_process = $i; /**Cập nhật số trang đã crawl */
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
                $storyData->s_status        = 1;
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

    // ------------------------------- Sửa lại cho phù hợp rồi dùng

    protected function getPageCategoryByUrl($category){
        $link = $category->c_link;
        CliEcho::infonl("");
        CliEcho::infonl("|== => [ 2 ] >START: Get Page By Category< == == == == == == == == == == == == == == == == == == ==|");
        CliEcho::warningnl("|-- -- - --[2] Link Category: " . $category->c_link);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($link, false, $context);

        $arrPage = [];
        foreach ($html->find('#main > div > div > section > footer > div > ul > li > a') as $item)
        {
            $page = $item->plaintext ?? '';
            if (preg_match('/^[0-9]+$/', trim($page)) && trim($page) != '') { 
                CliEcho::infonl("-- -- " . $item->plaintext); 
                $arrPage[] = trim($page); 
            }
            // $arrPage = trim($item->plaintext);
            
            sleep(1);
        }

        /**
         * 2. Update total page by category
         */
        $category->c_total_page = array_pop($arrPage);
        $category->save();
        // 3. Crawler link story detail
        CliEcho::infonl("|== => [ 2 ] >END: Get Page By Category< == == == == == == == == == == == == == == == == == == == =|");
        // $this->init();
        // $this->getStoryByCategory($category);
        CliEcho::infonl("");
    }
}
