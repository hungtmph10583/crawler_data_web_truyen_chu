<?php

namespace App\Console\Commands\Crawler;

use App\Models\Crawler\CrawlerCategory;
use App\Models\Crawler\CrawlerChapter;
use App\Models\Crawler\CrawlerStory;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Story;
use App\Traits\HelpersCrawlerTrait;
use App\HelpersClass\CliEcho;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrawlerChapterCommand extends Command
{
    use HelpersCrawlerTrait;
    protected $signature = 'crawler:chapter';
    protected $description = 'Command chapter';

    public function __construct(){ parent::__construct(); }
    public function handle(){ $this->init(); }

    /** Find the Story whose status is 0 (Default) */
    public function init()
    {
        $story = $this->getStoryWaiting();
        /** Check story status is 0 not exist */
        if (!$story) {
            CliEcho::warning('|=> [ 1 ] >Run Crawler Chapter Story Stop:');
            return;
        }
        /** Get the chapter name of the story with status is 0 */
        $this->getNameChapter($story);
    }

    /** Find the Story whose status is 0 (Default) */
    protected function getNameChapter($story)
    {
        $linkStory = $story->s_link;
        CliEcho::infonl("|== => [ 2 ] >START: Get Name Chapter< == == == == == == == == == == == == == == == == == == == == |");
        CliEcho::warningnl("|-- -- -- -[2] Link Story: " . $linkStory);
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($linkStory, false, $context);
        $loop = $story->s_total_page_chapter > 0 ? $story->s_total_page_chapter : 1;
        for ($i=0; $i < $loop; $i++) {
            foreach ($html->find('#list-chapter > div.row > div > ul > li > a') as $key => $item)
            {
                $cutString   = $item->find('span.chapter-text',0)->plaintext ?? '';
                $nameChapter = $item->plaintext ?? "NULL";
                $slugChapter = (trim(str_replace('/', '', str_replace($linkStory, '', $item->href))));
                $data = [
                    'c_name'        => 'Chương ' . (trim(str_replace($cutString, '', $nameChapter))),
                    'c_slug'        => $slugChapter,
                    'c_link_chapter'=> $item->href,
                    'c_story_id'    => $story->id,
                    'c_content'     => $this->getChapterDetail($item->href),
                    'created_at'    => Carbon::now('Asia/Ho_Chi_Minh')
                ];
                /** Lưu data chapter của truyện vào database */
                if (!$this->checkExistsChapter($slugChapter, $story)) {
                    dump('chapter chưa tồn tại');
                    $chapterCrawler = CrawlerChapter::create($data);
                    if ($chapterCrawler) { Chapter::create($data); }
                    CliEcho::successnl("|-- -- -- -[2] Insert Chapter Success");
                }else{
                    dump('chapter tồn tại');
                    CliEcho::errornl("|-- -- -- -[2] Error Inserting Chapter Because It Already Exists");
                }
                sleep(1);
            }
            sleep(1);
            
        }
        CliEcho::infonl("|== => [ 2 ] >END: Get Name Chapter< == == == == == == == == == == == == == == == == == == == == ==|");
        $this->init();
    }

    protected function getChapterDetail($domain)
    {
        CliEcho::infonl("|== == => [ 3 ] >Start: Get Chapter Detail< == == == == == == == == == == == == == == == == == == =|");
        $linkChapter = $domain;
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        $html = file_get_html($linkChapter, false, $context);
        foreach ($html->find('#chapter-c') as $key => $item){ $content = $item->plaintext; }
        // CliEcho::infonl($content);
        CliEcho::successnl("|-- -- -- -- -[3] Get Content Chapter Success");
        CliEcho::infonl("|== == => [ 3 ] >End: Get Chapter Detail< == == == == == == == == == == == == == == == == == == == =|");
        return($content);
    }
}
