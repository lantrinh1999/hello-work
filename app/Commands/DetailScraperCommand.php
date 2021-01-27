<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Nesk\Puphpeteer\Puppeteer;

class DetailScraperCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'detail:scraper';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command detail:scraper description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {
            $page = 1;
            while ($data = $this->getLinks($page)) {
                $this->info("Page $page");
                $this->getData($data);
                $page = $page + 1;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    protected function getLinks(int $page)
    {
        $limit = 100;
        $offset = ($page - 1) * $limit;
        return \DB::table('links')->limit($limit)->offset($offset)->orderBy('id', 'desc')->get();
    }

    protected function getData($datas)
    {
        foreach ($datas as $data) {
            try {
                $this->setStateValue();
                $puppeteer = new Puppeteer;
                $browser = $puppeteer->launch(['headless' => true, 'slowMo' => 100]);

                $page = $browser->newPage();
                $request = $page->goto($data['link'], ['waitUntil' => "load"]);
            } catch (\Throwable $th) {
                \Log::error($th->getMessage());
                throw $th;
            }
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
