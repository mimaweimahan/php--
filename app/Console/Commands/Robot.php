<?php

namespace App\Console\Commands;

use App\AccountLog;
use App\Currency;
use App\CurrencyMatch;
use App\Jobs\SendMarket;
use App\MarketHour;
use App\MyQuotation;
use App\MyQuotationSum;
use App\RobotPlan;
use App\Setting;
use App\Transaction;
use App\TransactionIn;
use App\TransactionOut;
use App\Users;
use App\UsersWallet;
use Faker\Factory;
use Illuminate\Console\Command;
use App\RobotExchange as RobotModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Robot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'robot {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配币币交易机器人';

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
        $this->info('开始执行机器人:' . now()->toDateTimeString());

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

            $this->info('当前法币是:' . $robot->legal_info);
            //查找订单
            $currencys = explode(',', $robot->currency_ids);
            $legal = Currency::find($robot->legal_id);

            foreach ($currencys as $currency_id) {
                $currency = Currency::find($currency_id);
                if ($currency) {
                    $this->info($currency->name . '/' . $legal->name);
                    $resu = strtolower($currency->name . $robot->legal_info);
                    $match_name = "market.{$resu}.kline.1min";
                    $obj = Redis::get($match_name);
                    $data = json_decode($obj, true);
                    if (is_null($data)) {
                        $this->info($match_name . '交易对未获取到价格');
                        continue;
                    }
                    $newprice = $data['tick']['close'];
                    $this->info($match_name . '最新价格是' . $newprice);
                    $low_price = bc_mul($newprice, (1 - $robot->min * 0.01), 8);
                    $high_price = bc_mul($newprice, (1 + $robot->max * 0.01), 8);
                    $this->info($match_name . '成交价格为' . "{$low_price}~{$high_price}");
                    $buyorders = TransactionIn::where('currency', $currency_id)
                        ->where('legal', $robot->legal_id)
                        ->whereBetween('price', [$low_price, $high_price])
                        ->where('create_time', '<=', time() - ($robot->mult * 60))
                        ->limit(10)->get()->toArray();
//                    var_dump($buyorders);
//                    continue;
                    foreach ($buyorders as $order)
                    {
                       $res = dealTrade($order['id'],'buy');
                       if($res['code']<0)
                       {
                           $this->info($order['id'].'处理失败'.$res['message']);
                       }else{
                           $this->info($order['id'].'处理成功');
                       }
                    }
                    $sellorders = TransactionOut::where('currency', $currency_id)
                        ->where('legal', $robot->legal_id)
                        ->whereBetween('price', [$low_price, $high_price])
                        ->where('create_time', '<=', time() - ($robot->mult * 60))
                        ->limit(10)->get()->toArray();
                    foreach ($sellorders as $order)
                    {
                        $res = dealTrade($order['id'],'sell');
                        if($res['code']<0)
                        {
                            $this->info($order['id'].'处理失败'.$res['message']);
                        }else{
                            $this->info($order['id'].'处理成功');
                        }
                    }
                }
            }

            $this->info('睡眠时间：' . $robot->second);
            sleep($robot->second);
        }

        $this->info('更新器执行结束:' . now()->toDateTimeString());
        $this->info('--------------------------------------------------');
    }


    /**
     * 获取整数时间
     */
    public static function getNowTime($type = '1min', $time = null)
    {
        $current = is_null($time) ? time() : $time;

        $yl = 60;
        if ($type == '5min') {
            $yl = 300;
        }
        if ($type == '15min') {
            $yl = 900;
        }
        if ($type == '30min') {
            $yl = 1800;
        }
        if ($type == '60min') {
            $yl = 3600;
        }

        $stamp = ($current % $yl) > 0 ? ($current - $current % $yl) : $current;

        if ($type == '1day') {
            $stamp = strtotime(date('Y-m-d', $current));
        }
        if ($type == '1week') {
            $stamp = strtotime('next Sunday', $current) - 60 * 60 * 24 * 7;
        }
        if ($type == '1mon') {
            $stamp = strtotime(date('Y-m', $current) . '-01');
        }
        return $stamp * 1000;
    }


}
