<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

class ScraperCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run:scraper {--state=} {--page=} {--headless=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command get all jobs on HELLO WORK JP';

    protected $state = '40'; //Fukuoka

    protected $prefectures = [];

    protected $baseURL = 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do?action=initDisp&screenId=GECA110010';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $puppeteer = new Puppeteer;
            $browser = $puppeteer->launch(['headless' => true, 'slowMo' => 100]);

            $page = $browser->newPage();
            $request = $page->goto($this->baseURL, ['waitUntil' => "load"]);

            if (!empty($state = $this->option('state'))) {
                $this->prefectures = [$state];
            } else {
                $this->prefectures = $page->evaluate(JsFunction::createWithBody($this->getPrefectureJs()));
            }
            foreach ($this->prefectures as $index => $prefecture) {
                if ($index > 0) {
                    $page = $browser->newPage();
                    $request = $page->goto($this->baseURL, ['waitUntil' => "load"]);
                }
                $this->info("Current selected prefecture: {$prefecture}");
                // select option
                $page->select('#ID_tDFK1CmbBox', $prefecture);
                // submit form
                $page->click("input#ID_searchBtn", ['waitUntil' => "load"]);
                sleep(10);
                $page->select('#ID_fwListNaviDispTop', '50');
                sleep(10);
                $current_number_page = $page->evaluate(JsFunction::createWithBody($this->cnpJS()));
                $data = $page->evaluate(JsFunction::createWithBody($this->getJs()));
                if (!empty($data) && count($data) > 0) {
                    $data = collect($data)->map(function ($item, $key) use ($prefecture) {
                        return array_merge($item, ['status' => 0, 'state_code' => $prefecture]);
                    })->all();
                    if (!empty(count($data))) {
                        $this->insertData($data);
                    }
                }

                // step 2
                $hasNextPage = $page->evaluate(JsFunction::createWithBody($this->hasNextPageJS()));

                while (true) {
                    $lastPage = false;
                    $this->info("Page " . $current_number_page);
                    $page->click("input[name=fwListNaviBtnNext]");
                    sleep(1);

                    $hasNextPage = $page->tryCatch->evaluate(JsFunction::createWithBody($this->hasNextPageJS()));
                    $current_number_page = $page->evaluate(JsFunction::createWithBody($this->cnpJS()));
                    for($i = 0; $i < 60; $i++) {
                        $this->info($current_number_page);
                        if (empty($current_number_page)) {
                            $this->info('Waiting...');
                            sleep(1);
                            $hasNextPage = $page->tryCatch->evaluate(JsFunction::createWithBody($this->hasNextPageJS()));
                            $current_number_page = $page->evaluate(JsFunction::createWithBody($this->cnpJS()));
                        } else {
                            break;
                        }
                    }

                    $data = $page->evaluate(JsFunction::createWithBody($this->getJs()));
                    $lastPage = $page->evaluate(JsFunction::createWithBody($this->lastPage()));

                    if($lastPage) {
                        $this->error('Is Last page. break loop');
                        break;
                    }
                    if (!empty($data) && count($data) > 0) {
                        $data = collect($data)->map(function ($item, $key) use ($prefecture) {
                            return array_merge($item, ['status' => 0, 'state_code' => $prefecture]);
                        })->all();
                        if (!empty(count($data))) {
                            $this->insertData($data);
                        }
                    } else {
                        break;
                    }
                    if((int) date('H') == 12) {
                        break;
                    }
                }
            }

            $browser->close();
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            throw $th;
        }
    }

    protected function setStateValue()
    {
        // dd($this->option('state'));
        if (!empty($state = $this->option('state'))) {
            $this->state = $state;
        }
        $this->info('Set state value = ' . $this->state);
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

    protected function getPrefectureJs()
    {
        return "
            const prefectures = [];
            const options = document.querySelectorAll('#ID_tDFK1CmbBox > option');
            if (options.length < 1) {
                return [];
            }
            options.forEach(option => {
                if (option.value && option.value.length > 0) {
                    prefectures.push(option.value);
                }
            });
            return prefectures;
        ";
    }

    protected function getJs()
    {
        return "
        let jobs = [];
        let job_wrapper = document.querySelectorAll('#ID_dispDetailBtn');
        if (job_wrapper.length < 1) {
            return;
        }
        job_wrapper.forEach((job) => {
            let dataJson = {};
            try {
                dataJson.link = job.href;
            } catch (err) {
                console.log(err);
            }
            jobs.push(dataJson);
        });
        return jobs;
        ";
    }

    protected function cnpJs()
    {
        return "
        if(document.querySelectorAll('input[name=fwListNowPage]:not([disabled])').length) {
            return document.querySelector('input[name=fwListNowPage]:not([disabled])').value
        } return false;
        ";
    }

    protected function nextPageBtn()
    {
        return "
        if(document.querySelectorAll('input[name=fwListNaviBtnNext]:not([disabled])').length) {
            return document.querySelector('input[name=fwListNaviBtnNext]:not([disabled])').value
        } return false;
        ";
    }

    protected function lastPage()
    {
        return "
        if(document.querySelectorAll('input[name=fwListNaviBtnNext]:is([disabled])').length) {
            return true;
        } return false;
        ";
    }

    protected function hasNextPageJs()
    {
        return "
        if (document.querySelectorAll('input[name=fwListNaviBtnNext]:not([disabled])').length) {
            return document.querySelector('input[name=fwListNaviBtnNext]:not([disabled])').value
        } return false;
        ";
    }

    protected function insertData(array $data)
    {

        $jobs = [];
        foreach ($data as $job) {
            $check = \DB::table('links')->where('link', $job['link'])->exists();
            if (!$check) {
                $job['created_at'] = $job['updated_at'] = now();
                $jobs[] = $job;
                $this->info("+ New job: " . $job['link']);
            }
        }
        if (empty(count($jobs))) {
            return false;
        }
        $a = \DB::table('links')->insert($jobs);
        if ($a) {
            $this->info('- Insert OK');
        }
    }
}
