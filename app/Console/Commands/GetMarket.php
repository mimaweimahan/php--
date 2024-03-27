<?php

namespace App\Console\Commands;

use App\CurrencyMatch;
use App\Http\Controllers\Admin\MyQuotation;
use App\Market;
use App\MyQuotationSum;
use App\Robot as RobotModel;
use App\Utils\RPC;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_market {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '归集行情';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->info('--------------------------------------------------');
        $this->info('开始执行collect:' . now()->toDateTimeString());

        $id = $this->argument('id');
        while (true) {
            $robot = RobotModel::find($id);
            if (!$robot) {
                $this->info('找不到此机器人');
                break;
            }

            if ($robot->status == RobotModel::STOP) {
                $this->info('机器人已关闭');
                break;
            }
            $currencys = CurrencyMatch::where('market_from', 3)->get()->toArray();
            foreach ($currencys as $currency) {
                $base = $currency['currency_name'];
                //处理五分钟
                $this->collect('5min', $base);
                //处理十五分钟
                $this->collect('15min', $base);
                //处理30分钟
                $this->collect('30min', $base);
                //处理60分钟
                $this->collect('60min', $base);
                //处理一天
                $this->collect('1day', $base);
                //处理一周
                $this->collect('1week', $base);
                //处理一月
                $this->collect('1mon', $base);
            }
            sleep(4);
        }
    }

    public function collect($type, $base)
    {
        $min = 0;
        switch ($type) {
            case '5min':
                $min = '+5 minutes';
                break;
            case '15min':
                $min = '+15 minutes';
                break;
            case '30min':
                $min = '+30 minutes';
                break;
            case '60min':
                $min = '+1 hour';
                break;
            case '1day':
                $min = '+1 day';
                break;
            case '1week':
                $min = '+1 week';
                break;
            case '1mon':
                $min = '+1 month';
                break;
        }
        //找到最新的记录
        $last = MyQuotationSum::where('ktype', $type)->where('base', $base)->orderBy('itime', 'desc')->first();
//        var_dump($last->toArray());
//        die;
        if ($last) {
            $last = $last->toArray();
            if (strtotime($last['itime']) == intval(Robot::getNowTime($type) / 1000)) {
                //如果是最新一条记录 则更新该记录
                $start = Robot::getNowTime($type, strtotime($last['itime']));
                $start_ed = intval($start / 1000);
                $end_ed = time();
                $sum = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->sum('vol');
                $high = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->max('high');
                $low = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->min('low');
                $open = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'asc')->value('open');
                $close = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'desc')->value('close');
                $exists = MyQuotationSum::where('ktype', $type)->where('itime', $start_ed)->where('base', $base)->first();
                $my_sum = $exists ? $exists : new MyQuotationSum();
                $my_sum->ktype = $type;
                $my_sum->open = $open;
                $my_sum->close = $close;
                $my_sum->high = $high;
                $my_sum->low = $low;
                $my_sum->vol = $sum;
                $my_sum->symbol = "{$base}/USDT";
                $my_sum->created_at = date('Y-m-d H:i:s');//time();
                $my_sum->updated_at = date('Y-m-d H:i:s');
                $my_sum->base = $base;
                $my_sum->target = 'USDT';
                $my_sum->itime = date('Y-m-d H:i:s',$start_ed);
                var_dump($my_sum->itime);
                $my_sum->save();
            } else {

                $start = Robot::getNowTime($type, strtotime($min,strtotime($last['itime'])));
                $start_ed = intval($start / 1000);
                $end_ed = strtotime($min, $start_ed);
                $sum = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->sum('vol');
                $high = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->max('high');
                $low = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->min('low');
                $open = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'asc')->value('open');
                $close = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'desc')->value('close');
                $exists = MyQuotationSum::where('ktype', $type)->where('itime', $start_ed)->where('base', $base)->first();
                $my_sum = $exists ? $exists : new MyQuotationSum();
                $my_sum->ktype = $type;
                $my_sum->open = $open;
                $my_sum->close = $close;
                $my_sum->high = $high;
                $my_sum->low = $low;
                $my_sum->vol = $sum;
                $my_sum->symbol = "{$base}/USDT";
                $my_sum->created_at = date('Y-m-d H:i:s');//time();
                $my_sum->updated_at = date('Y-m-d H:i:s');
                $my_sum->base = $base;
                $my_sum->target = 'USDT';
                $my_sum->itime = date('Y-m-d H:i:s',$start_ed);
                var_dump($my_sum->itime);
                $my_sum->save();
            }
        } else {
            //第一次
            $last_quotation = \App\MyQuotation::where('base', $base)->orderBy('itime', 'asc')->limit(1)->first();
            if ($last_quotation) {
                $last_quotation = $last_quotation->toArray();
                $start = Robot::getNowTime($type, strtotime($last_quotation['itime']));
                $start_ed = intval($start / 1000);
                $end_ed = strtotime($min, $start_ed);
                if ($end_ed > time()) {
                    $end_ed = time();
                }
                $sum = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->sum('vol');
                $high = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->max('high');
                $low = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->min('low');
                $open = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'asc')->value('open');
                $close = \App\MyQuotation::where('base', $base)->whereBetween('itime', [$start_ed, $end_ed])->orderBy('itime', 'desc')->value('close');
                $my_sum = new MyQuotationSum();
                $my_sum->ktype = $type;
                $my_sum->open = $open;
                $my_sum->close = $close;
                $my_sum->high = $high;
                $my_sum->low = $low;
                $my_sum->vol = $sum;
                $my_sum->symbol = "{$base}/USDT";
                $my_sum->created_at = date('Y-m-d H:i:s');//time();
                $my_sum->updated_at = date('Y-m-d H:i:s');
                $my_sum->base = $base;
                $my_sum->target = 'USDT';
                $my_sum->itime = date('Y-m-d H:i:s',$start_ed);
                var_dump($my_sum->itime);
                $my_sum->save();
            }
        }
        //找到最早的记录
        //查找是否有超出最新的记录
        //查找是否有最久的记录
    }
}
