<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WealthTransaction extends Model
{
    protected $table = 'wealth_transaction';
    public $timestamps = false;



    const STATUS0 = 0; //已删除
    const STATUS1 = 1; //进行中
    const STATUS2 = 2; //已赎回
    const STATUS3 = 3; //已到期
    const STATUS4 = 4; //计算收益中

    protected static $statusList = [
        '已删除',
        '进行中',
        '已赎回',
        '已到期',
        '计算收益中'
    ];

    protected $appends = [
        'show_status',
        'show_add_time',
        'user',
        'product',
        'show_end_time',
        'show_last_time'
    ];

    public function user()
    {
        return $this->belongsTo('App\Users', 'user_id', 'id')->withDefault();
    }

    public function wealthProduct(){
        return $this->belongsTo('App\WealthProduct', 'wealth_product_id', 'id')->withDefault();
    }
    public function getUserAttribute()
    {
        return Users::find($this->attributes['user_id'],['id','phone','account_number']);//->values('account_number,phone');
    }

    public function getProductAttribute()
    {
        return WealthProduct::find($this->attributes['wealth_product_id']);
    }
    public function getShowStatusAttribute(){
        $value = isset(self::$statusList[$this->attributes['status']]) ? self::$statusList[$this->attributes['status']]:"未知状态";
        return $value;
    }

    public function getShowAddTimeAttribute(){
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getShowEndTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        $value += $this->attributes['period']*3600*24;
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }



    public function getShowLastTimeAttribute()
    {
        $value = $this->attributes['last_calc_time'];

        return $value>0 ? date('Y-m-d H:i:s', $value) : '-';
    }

    public static function calcWealth(){
        /**
         * 1. 寻找最后计算收益小于今天0点数据
         */
        $calcTime = strtotime(date("Y-m-d"),time());
        $query = WealthTransaction::where('status',WealthTransaction::STATUS1)->get();

        foreach ($query as $trade){
            WealthTransaction::doWealthTrade($trade);
        }

    }

    //处理收益
    public static function doWealthTrade($trade){
        $calcTime = strtotime(date("Y-m-d"),time());

        try{


            //处理需要计算收益的数据
            // if(date('Y-m-d',$trade->last_calc_time)==date('Y-m-d'))
            // {
            //     //如果最后计算收益日期是今天，那么已经结算过了，退出今天
            //     echo '计算日期时今日 不计算';
            //     return;
            // }
            // if(strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',$trade->create_time))<=0)
            // {
            //     //隔日才会开始计算
            //     echo '未满二十四小时 不计算'.$trade->id;
            //     return;
            // }

            if($trade->expire_time-86400 < time())
            {
                DB::beginTransaction();
                $user_change = UsersWallet::where("user_id", $trade->user_id)
                    ->where("currency", $trade->product->currency) //3为USDT
                    ->lockForUpdate()
                    ->first();

                WealthTransaction::where('id',$trade->id)->update(['status'=>WealthTransaction::STATUS3]); //先把状态改成
                //解除质押
                change_wallet_balance($user_change,2,($trade->caution_money)*-1,AccountLog::LOCK_REMAIN_BALANCE,'Maturity of pledge',true);
                change_wallet_balance($user_change,2,$trade->product->min_daily_return_rate+$trade->caution_money,AccountLog::LOCK_BALANCE,'Maturity of pledge');
                DB::commit();
                return;
            }else{
                echo '未到起息日 不计算'.$trade->id.'('.date('Y-m-d H:i:s',$trade->expire_time).')';
            }
            return;


            DB::beginTransaction();
            WealthTransaction::where('id',$trade->id)->update(['status'=>WealthTransaction::STATUS4]); //先把状态改成

            $rate = mt_rand($trade->min_daily_return_rate*100,$trade->max_daily_return_rate*100);
            $revenue = $trade->caution_money * ($rate/100); //算出收益

            //last_calc_time + 86400 增加一天的时间 万一某一天跑批程序没执行 这样可以吧没执行的天找出来
            WealthTransaction::where('id',$trade->id)
                ->update(['revenue'=>"+{$revenue}","last_calc_time"=>time(),
                    'status'=>WealthTransaction::STATUS1]);

            $user_change = UsersWallet::where("user_id", $trade->user_id)
                ->where("currency", $trade->product->currency) //3为USDT
                ->lockForUpdate()
                ->first();

            //增加锁仓余额
//            $user_change->lock_change_balance = bc_add($user_change->lock_change_balance, $revenue, 5);
            change_wallet_balance($user_change,2,$revenue,AccountLog::LOCK_BALANCE,'Lock in interest increased',true);//锁仓利息增加
//            $user_change->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            Log::info("doWealthTrade  failed id:{$trade->id}, error:{$e->getMessage()}");
        }




        //如果过期就把产品修改成过期状态,归还本金
        $expire_time = strtotime(date("Y-m-d"),$trade->expire_time);
        if($expire_time <= $calcTime){
            try{
                DB::beginTransaction();
                WealthTransaction::where('id',$trade->id)->update(['status'=>WealthTransaction::STATUS3]);
                $user_change = UsersWallet::where("user_id", $trade->user_id)
                    ->where("currency", $trade->product->currency) //3为USDT
                    ->lockForUpdate()
                    ->first();

                $user_change->change_balance = bc_add($user_change->change_balance, $trade->caution_money, 5);
                $user_change->lock_wealth_balance = bc_sub($user_change->lock_wealth_balance, $trade->caution_money, 5);
                $user_change->save();

                DB::commit();
            }catch (\Exception $e){
                DB::rollback();
                Log::info("doWealthTrade  expire failed id:{$trade->id}, error:{$e->getMessage()}");
            }

            return;
        }


    }



}
