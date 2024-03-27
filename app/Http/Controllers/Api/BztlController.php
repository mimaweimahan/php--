<?php

namespace App\Http\Controllers\Api;

use App\Users;
use App\UsersWallet;
use App\BztlProduct;
use App\BztlTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\AccountLog;

class BztlController extends Controller
{
    public function getMyprofit(){
        $status = Input::get('type',0);
        $user_id = Users::getUserId();
        $limit = 10000;//Input::get("limit", 10000);
        $page = Input::get("page", 1);
        if(empty($user_id)){
            return $this->error("参数错误");
        }

        $query = BztlTransaction::where(function ($query) use ($status){
            if($status >0) {
                $query->where('status',$status);
            }
        })->where('user_id',$user_id)->orderBy('create_time','desc')->paginate($limit);

//        $list = [];
//
//        if($query){
//            foreach ($query as $trade){
//                $shengyu_days = intval(($trade->expire_time - time())/86400);
//
//                array_push($list,[
//                    'id'=>$trade->id,
//                    'caution_money'=>$trade->caution_money,
//                    'period'=>$trade->period,
//                    'day_revenue'=>$trade->day_revenue,
//                    'revenue'=>$trade->revenue,
//                    'shengyu_days'=>$shengyu_days,
//                    'show_add_time'=>$trade->show_add_time
//                ]);
//            }
//        }

        return $this->success($query);

    }

    public function sellProfit(){
        $id = Input::get('profit',0);
        $user_id = Users::getUserId();

        if(empty($id) || empty($user_id)){
            return $this->error('参数错误');
        }


        $wealthTrade  = BztlTransaction::where('user_id',$user_id)->where('id',$id)->first();
        if(empty($wealthTrade)){
            return $this->error('订单不存在');
        }

        try{
            DB::beginTransaction();

            $user_change = UsersWallet::where("user_id", $user_id)
                ->where("currency", 3) //3为USDT
                ->lockForUpdate()
                ->first();
            $shengyu_days = intval(($wealthTrade->expire_time - time())/86400);
            $kc = (($wealthTrade->product->reneged * 100) * $shengyu_days *  $wealthTrade->caution_money) / 100;
            $breach =  $wealthTrade->caution_money - $kc;
            // dd($breach);
            $wealthTrade->status = BztlTransaction::STATUS2;
            $wealthTrade->save();
            $user_change->change_balance = bc_add($user_change->change_balance, $breach, 5);
            $user_change->lock_change_balance = bc_sub($user_change->lock_change_balance, $breach, 5);
            $user_change->save();
            change_wallet_balance($user_change,2,$kc * -1,AccountLog::LOCK_REMAIN_BALANCE,'搬砖提前赎回扣除 Maturity of pledge',true);
            // change_wallet_balance($user_change,2,$breach,AccountLog::LOCK_BALANCE,'搬砖提前赎回返本 Maturity of pledge');
            DB::commit();
            return $this->success("操作成功");

        }catch (\Exception $e){
            DB::rollback();
            return $this->error($e->getMessage());
        }

    }

    public function buyProfit(){
        $id = Input::get('id',0);
        $user_id = Users::getUserId();
        $amount = Input::get('number',0);
        if(empty($id) || empty($amount) || empty($user_id)){
            return $this->error("参数错误");
        }

        $pinfo = BztlProduct::where('id',$id)->first();
        if(empty($pinfo)){
            return $this->error("产品不存在");
        }

        $amount = $pinfo->min_single_limit;

        $exists = BztlTransaction::where("user_id", $user_id)->where('bztl_product_id',$id)->count();
        if($exists>=$pinfo->nlimit)
        {
            return $this->error('超出限购次数');
        }

        try{
            DB::beginTransaction();

            $user_change = UsersWallet::where("user_id", $user_id)
                ->where("currency", $pinfo->currency) //3为USDT
                ->lockForUpdate()
                ->first();

            if(bc_comp($user_change->change_balance,$amount) < 0){
                throw new \Exception('余额不足');
            }


            $now = time();
            $order = new BztlTransaction();
            $order->user_id = $user_id;
            $order->bztl_product_id = $id;
            $order->period = $pinfo->period;
            $order->max_daily_return_rate = $pinfo->max_daily_return_rate;
            $order->min_daily_return_rate = $pinfo->min_daily_return_rate;
//            $order->reneged = $pinfo->reneged;
            $order->caution_money = $amount;
            $order->status	 = BztlTransaction::STATUS1;
            $order->expire_time	 = strtotime('+ '.$pinfo->period.' day');
            $order->create_time = $now;
            $order->save();

            $user_change->change_balance = bc_sub($user_change->change_balance, $amount, 5);
            $user_change->lock_change_balance = bc_add($user_change->lock_change_balance, $amount, 5);
            $user_change->save();
            DB::commit();
            return $this->success("操作成功");

        }catch (\Exception $e){
            DB::rollback();
            return $this->error($e->getMessage());
        }


    }

    //
    public function getProfitTotal(Request $request){
        $user_id = Users::getUserId();
        $legal_id = $request->get("legal_id");
        $pid = $request->get('pid',0);

        if (empty($legal_id) || empty($user_id)) {
            return $this->error("参数错误");
        }
        $user_wealth_legal = 0;
        $user_change_balance = 0;
        $legal = UsersWallet::where("user_id", $user_id)->where("currency", $legal_id)->first();
        if ($legal) {
            $user_wealth_legal = $legal->lock_wealth_balance;
            $user_change_balance = $legal->change_balance;
        }


        $wealthTrade = BztlTransaction::where('user_id',$user_id)->where('status',1)->get();
        $totalRevenue = 0;// 总收益
        $dayRevenue = 0; //日收益
        $totalTradeNum = 0; //托管订单总数
        if($wealthTrade){
            foreach ($wealthTrade as $trade){
                $totalRevenue += $trade->revenue;
                if($trade->status){
                    $dayRevenue += $trade->day_revenue;
                    $totalTradeNum+=1;
                }
            }
        }

        $money_limit = '';
        $rate_win = '';
        $rate_back = 0;
        $introduction = '';
        $pname = '';
        if(!empty($pid)){
            $winfo = BztlProduct::where('id',$pid)->first();
            if($winfo){
                $money_limit = $winfo->single_limit;
                $rate_win = $winfo->daily_return_rate;
                $rate_back = $winfo->reneged;
                $introduction = $winfo->introduction;
                $pname= $winfo->wealth_name;
            }
        }

        return $this->success([
            'user_change_balance'=>$user_change_balance,
            'total_amount'=> $user_wealth_legal,
            'total_profit'=>$totalRevenue,
            'day_profit'=>$dayRevenue,
            'order_cnt'=>$totalTradeNum,
            'money_limit'=>$money_limit,
            'rate_win'=>$rate_win,
            'rate_back'=>$rate_back,
            'introduction'=>$introduction,
            'name'=>$pname
        ]);

    }

    public function getProfitList(){
        $wealthProduct = BztlProduct::where('status',1)->get();
        $list = [];
        if($wealthProduct){
            foreach ($wealthProduct as $p){
                array_push($list,[
                    'id'=>$p->id,
                    'name'=>$p->wealth_name,
                    'rate_win'=>$p->daily_return_rate,
                    'moneylimit'=>$p->single_limit
                ]);
            }
        }

        return $this->success($wealthProduct);
    }
}
