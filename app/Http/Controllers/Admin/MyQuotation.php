<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\CurrencyMatch;
use App\MarketHour;
use App\MyQuotationSum;
use App\WealthTransaction;
use Faker\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MyQuotation as NeedleModel;

class MyQuotation extends \App\Http\Controllers\Admin\Controller
{
    //
    public function index()
    {
        $data = [
            'currencys' => $currencys = CurrencyMatch::where('market_from', 3)->get()
        ];
        return view('admin.needle.quotation', [
            'data' => $data,
        ]);
    }

    private function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    public function generate(Request $request)
    {
        $numbers = $request->get('insert_range');
        $start = $request->get('start');
        $rate1 = $request->get('rate1');
        $rate2 = $request->get('rate2');
        $speed0 = $request->get('speed0');
        $speed1 = $request->get('speed1');
        $jingdu = $request->get('jingdu');
        $minutes = $request->get('dates') . ':00';
        $now = [];
        $vol0 = $request->get('vol0');
        $vol1 = $request->get('vol1');

        $faker = Factory::create();

        for ($i = 0; $i < $numbers; $i++) {

            if ($i === 0) {
                $now[] = $start;
            } else {
                $rand = $faker->randomFloat($jingdu, $speed0, $speed1);;
                if ($this->get_rand([$rate2, $rate1]) === 1) {
                    $now[] = $now[$i - 1] + $rand;
                } else {
                    $now[] = $now[$i - 1] - $rand;
                }
            }
        }

        $obj = [];
        foreach ($now as $number) {


            $arr = ['open' => count($obj) === 0 ? $start : $obj[count($obj) - 1]['close']];

            $arr['close'] = $number + $faker->randomFloat($jingdu, $speed0, $speed1);
            if (count($obj) === 0) {
                $arr['close'] = $number + $faker->randomFloat($jingdu, $speed0, $speed1);;
            }


            $val = max(array_values($arr));
            $minVal = min(array_values($arr));

            $arr['high'] = $val + $faker->randomFloat($jingdu, $speed0, $speed1) * $faker->randomFloat(2, 0.5, 0.50);
            $arr['low'] = $minVal - $faker->randomFloat($jingdu, $speed0, $speed1) * $faker->randomFloat(2, 0.05, 0.50);

            array_walk($arr, function (&$val) use ($jingdu) {
                $val = sprintf('%.' . $jingdu . 'f', $val);
            });
            $obj[] = $arr;

        }

        $rsp = [];
        $i = 0;
        foreach ($obj as $v) {
            $rsp[] = [date('Y-m-d H:i:00', strtotime("+{$i} minutes", strtotime($_GET['dates']))), $v['open'], $v['high'], $v['low'], $v['close'], rand($vol0, $vol1)];

            $i++;
        }
        return ['data' => $rsp, 'next' => date('Y-m-d H:i', strtotime('+' . $numbers . ' minutes', strtotime($_GET['dates'] . ':00')))];
    }

    public function lists(Request $request)
    {

        $limit = $request->input("limit", 20);


        $query = \App\MyQuotation::where('base', $request->get('currency'))->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($query);
    }

    public function delete(Request $request)
    {

        $currency = $request->get('currency');
        $start = $request->get('start') . ':00';
        $end = $request->get('end') . ':00';
        $start = strtotime($start);
        $end = strtotime($end);

        if ($start < time() || $end < $start) {
            return ['code' => -1];
        }
        \App\MyQuotation::where('base', $currency)->whereBetween('itime', [$start, $end])->delete();
        return ['code' => 1];
    }

    public function reset(Request $request)
    {
        $currency = $request->get('currency');
        \App\MyQuotation::where('base', $currency)->delete();
        MyQuotationSum::where('base', $currency)->delete();
        return ['code' => 1];
    }

    public function preview(Request $request)
    {
        $currency = $request->get('currency');
        $start = $request->get('start') . ':00';
        $end = $request->get('end') . ':00';
        $start = strtotime($start);
        $end = strtotime($end);

        $list = \App\MyQuotation::where('base',$currency)->whereBetween('itime',[$start,$end])->select(['open','high','low','close','itime','vol'])->get();
        $list=$list->toArray();

        $obj = [];
        foreach($list as $v)
        {
            $ob = [$v['itime'], floatval($v['open']), floatval($v['high']), floatval($v['low']), floatval($v['close']),floatval($v['vol'])];
            $obj[] = $ob;
        }
        return view('admin.needle.preview', [
            'data' => $obj,
        ]);
    }

    public static function needleList($num = 0, $name)
    {
        $esclient = MarketHour::getEsearchClient();

        $param = [
            'index' => 'market.quotation',
            'type' => 'doc',//$type,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['base' => $_POST]],
                            ['match' => ['base-currency' => $base_currency]],
                            ['match' => ['quote-currency' => $quote_currency]],
                        ],
                        'filter' => [
                            'range' => [
                                'id' => [
                                    'gte' => $from,
                                    'lte' => $to,
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                ],
                'size' => 20,
                'from' => 20
            ],
        ];
        $result = $esclient->search($param);
        if (isset($result['hits'])) {
            $data = [];
//            foreach($result['hits']['hits'] as $val)
//            {
//                $val['_source'];
//            }
            $data = array_column($result['hits']['hits'], '_source');

            return $data;
        } else {
            return [];
        }
//        $news_query = NeedleModel::where(function ($query) use ($cId) {
//            $cId > 0 && $query->where('id', $cId);
//        })->orderBy('id', 'desc');
        $news = $num != 0 ? $news_query->paginate($num) : $news_query->get();
        return $news;
    }
}
