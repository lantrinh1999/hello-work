<?php

namespace App\Commands;

use Goutte\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use Symfony\Component\DomCrawler\Crawler;

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

    protected $data = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $page = 1;
        try {
            while ($data = $this->getLinks($page)) {
                $this->info("Page $page");
                // $this->getData2($data);
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

    protected function handleData($data)
    {
        return $this->data[] = $data;
    }
    protected function resetData()
    {
        return $this->data = [];
    }

    protected function getDataCrawl()
    {
        return $this->data;
    }

    protected function getData($datas)
    {
        foreach ($datas as $data) {
            try {
                $this->info($data->link);
                $this->resetData();

                $puppeteer = new Puppeteer;
                $browser = $puppeteer->launch(['headless' => true, 'slowMo' => 100]);
                $page = $browser->newPage();
                $request = $page->goto($data->link, ['waitUntil' => "load"]);

                $job = $page->evaluate(JsFunction::createWithBody($this->jS()));
                for ($i = 0; $i < 30; $i++) {
                    $this->info(1);
                    if ($job) {
                        break;
                    } else {
                        $job = $page->evaluate(JsFunction::createWithBody($this->jS()));
                    }
                }
                $job = [];
                $data_ = $page->evaluate(JsFunction::createWithBody('return document.documentElement.outerHTML'));
                $browser->close();
                $crawler = new Crawler($data_);
                $table = $crawler->filter('.normal.mb1');
                if (!empty($table->count())) {
                    $table->each(function ($node) use ($job) {
                        $tr = $node->filter('tr');
                        if (!empty($tr->count())) {
                            $tr->each(function ($no) use ($job) {
                                $th = $no->filter('th')->text();
                                $td = $no->filter('td')->text();
                                $this->handleData(compact('th', 'td'));
                            });
                        }
                    });
                    $job = $this->getDataCrawl();

                    if (!empty(count($job))) {
                        $dataInsert = [];
                        $dataOffice = [];
                        foreach ($job as $item) {
                            if ($item['th'] == '求人番号') {
                                $dataInsert['job_number'] = $item['td'];
                            }

                            if ($item['th'] == '受付年月日') {
                                $dataInsert['reception_date'] = $item['td'];
                            }

                            if ($item['th'] == '紹介期限日') {
                                $dataInsert['referral_deadline'] = $item['td'];
                            }

                            if ($item['th'] == '求人区分') {
                                $dataInsert['type'] = $item['td'];
                            }

                            if ($item['th'] == 'トライアル雇用併用の希望') {
                                $dataInsert['hope'] = $item['td'];
                            }

                            if ($item['th'] == 'ａ ＋ ｂ（固定残業代がある場合はａ ＋ ｂ ＋ ｃ）') {
                                $dataInsert['wage'] = $item['td'];
                            }

                            if ($item['th'] == '休日等') {
                                $dataInsert['holiday'] = $item['td'];
                            }

                            if ($item['th'] == '職種') {
                                $dataInsert['occupation'] = $item['td'];
                            }

                            if ($item['th'] == '仕事内容') {
                                $dataInsert['job_description'] = $item['td'];
                            }

                            if ($item['th'] == '派遣・請負等') {
                                $dataInsert['contract'] = $item['td'];
                            }

                            if ($item['th'] == '雇用期間') {
                                $dataInsert['employment_period'] = $item['td'];
                            }

                            if ($item['th'] == '就業場所') {
                                $dataInsert['work_place'] = $item['td'];
                            }

                            if ($item['th'] == 'マイカー通勤') {
                                $dataInsert['private_car_commute'] = $item['td'];
                            }

                            if ($item['th'] == '転勤の可能性') {
                                $dataInsert['possibility_of_transfer'] = $item['td'];
                            }

                            if ($item['th'] == '年齢') {
                                $dataInsert['age'] = $item['td'];
                            }

                            if ($item['th'] == '学歴') {
                                $dataInsert['educational_background'] = $item['td'];
                            }

                            if ($item['th'] == '必要な経験等') {
                                $dataInsert['required'] = $item['td'];
                            }

                            if ($item['th'] == '必要な免許・資格') {
                                $dataInsert['license'] = $item['td'];
                            }

                            // office_number
                            // office_name
                            // location
                            // home_page

                            if ($item['th'] == '事業所番号') {
                                $dataOffice['office_number'] = $item['td'];
                            }

                            if ($item['th'] == '事業所名') {
                                $dataOffice['office_name'] = $item['td'];
                            }

                            if ($item['th'] == '所在地') {
                                $dataOffice['location'] = $item['td'];
                            }

                            if ($item['th'] == '法人番号') {
                                $dataOffice['company_number'] = $item['td'];
                            }

                            // if($item['th'] == '所在地') {
                            //     $dataOffice['location'] = $item['td'];
                            // }

                        }

                        $dataInsert['created_at'] = $dataInsert['updated_at']
                        = $dataOffice['created_at'] = $dataOffice['created_at'] = date('Y-m-d H:i:s');
                        if (!empty($dataOffice['office_number'])) {
                            $office = \DB::table('recruiting_offices')
                                ->where('office_number', $dataOffice['office_number'])->first();
                            if (!$office) {
                                $office_id = \DB::table('recruiting_offices')->insertGetId($dataOffice);
                            } else {
                                $office_id = $office->id;
                            }
                            $dataInsert['recruiting_office_id'] = $office_id;
                        }

                        $jobInsert = \DB::table('jobs')->insert($dataInsert);
                        if ($jobInsert) {
                            $id = $data->id;
                            $check = DB::table('links')
                                ->where('id', $id)
                                ->update(['status' => 1]);
                        } else {
                            $this->info('Insert JOB FAIL');
                        }

                    }
                }

            } catch (\Throwable $th) {
                dd($th->getMessage());
                \Log::error($th->getMessage());
            }
        }
    }

    protected function jS()
    {
        return "
        if(document.querySelectorAll('.normal.mb1 tr').length) {
            return true;
        } return false;
        ";
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
