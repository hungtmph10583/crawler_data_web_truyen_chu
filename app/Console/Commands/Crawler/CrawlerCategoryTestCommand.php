<?php

namespace App\Console\Commands\Crawler;

use Illuminate\Console\Command;
use App\Traits\HelpersCrawlerTrait;

class CrawlerCategoryTestCommand extends Command
{

    use HelpersCrawlerTrait;
    protected $signature = 'crawler:crawlerCategory';
    protected $description = 'Crawler';

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

    }
}
