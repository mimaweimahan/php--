<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\LeverTransaction;
use App\MicroOrder;
use App\UsersRecordMark;
use App\UsersWallet;
use App\UsersWalletOut;
use App\WealthTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Admin;
use App\AdminRole;
use App\AdminRolePermission;
use App\Users;

class DefaultController extends Controller
{

    public function login()
    {
        $username = Input::get('username', '');
        $password = Input::get('password', '');
        if (empty($username)) {
            return $this->error('用户名必须填写');
        }
        if (empty($password)) {
            return $this->error('密码必须填写');
        }
        $password = Users::MakePassword($password);
        $admin = Admin::where('username', $username)->where('password', $password)->first();
        if (empty($admin)) {
            return $this->error('用户名密码错误');
        } else {
            $role = AdminRole::find($admin->role_id);
            if (empty($role)) {
                return $this->error('账号异常');
            } else {
                session()->put('admin_username', $admin->username);
                session()->put('admin_id', $admin->id);
                session()->put('admin_role_id', $admin->role_id);
                session()->put('admin_is_super', $role->is_super);
                return $this->success('登陆成功');
            }
        }
    }

    /**
     * 自动生成列表数据
     */
    public function autoproduce(){
        $r_date =  Input::get('r_date', '');
        $token =  Input::get('token', '');

        if($token!='20000002'){
            return;
        }

        $UsersRecordMark  =  new UsersRecordMark();  //获取到用户账户的记录

        if($r_date=='2022-07-01'){
            $get =  $UsersRecordMark->where('r_date', '=', $r_date)->orderBy('id','desc')->first();
        }else{
            $get =  $UsersRecordMark->orderBy('id','desc')->first();
        }

        if($get){
            if(date('Y-m-d',time()) == $get->r_date){
                $r_date = date('Y-m-d',time());
                $get = $get;
            }else{
                $r_date = date('Y-m-d',strtotime($get->r_date)+(25*60*60));

                $get =  $UsersRecordMark->where('r_date', '=', $r_date)->orderBy('id','desc')->first();
            }
        }


        //计算当前数据
        // $now_date = Carbon::now()->toDateString();
        // DB::connection()->enableQueryLog();
        //  $logs = DB::getQueryLog();
        $starttime = strtotime($r_date.' 00:00:00');
        $endtime  = strtotime($r_date.' 23:59:59');

        //新注册
        $r_register_count = DB::table('users')
            ->whereBetween('time',[$starttime,$endtime])
            ->count();
        //活跃用户
        $r_active_count = DB::table('users')
            ->whereBetween('lasttime',[$starttime,$endtime])
            ->count();

        //下单人数; 杠杆，（秒)合约交易，搬砖套利
        $r_order_count_1 = LeverTransaction::whereBetween('create_time',[$starttime,$endtime])->groupBy('user_id')->count();
        $r_order_count_2 = MicroOrder::whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->groupBy('user_id')->count();
        $r_order_count_3 = WealthTransaction::whereBetween('create_time',[$starttime,$endtime])->groupBy('user_id')->count();

        //下单总额
         $r_order_money_1 =  LeverTransaction::whereBetween('create_time',[$starttime,$endtime])->sum('caution_money');
         $r_order_money_2 = MicroOrder::whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->sum('number');
         $r_order_money_3 = WealthTransaction::whereBetween('create_time',[$starttime,$endtime])->sum('caution_money');

         //下单手续费
         $r_order_charge_1 = LeverTransaction::whereBetween('create_time',[$starttime,$endtime])->sum('trade_fee');
         $r_order_charge_2 = MicroOrder::whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->sum('fee');

         //盈亏总额
         $r_profit_loss_1 =  LeverTransaction::whereBetween('handle_time',[$starttime,$endtime])->sum('fact_profits'); //最终盈亏
         $r_profit_loss_2 =  MicroOrder::whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->sum('fact_profits'); //盈利字段；

        //充值人数；
        $r_recharge_count = DB::table('charge_req')->whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->groupBy('uid')->count();

        //充值总额；
        $r_recharge_sum = DB::table('charge_req')->whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])->sum('amount');

       //实际充值金额
        $r_recharge_true = DB::table('charge_req')
                                ->where(['status'=>'2'])
                                ->whereBetween('created_at',[$r_date.' 00:00:00',$r_date.' 23:59:59'])
                                ->sum('amount');
        //提现人数
        $r_with_number = UsersWalletOut::whereBetween('create_time',[$starttime,$endtime])->groupBy('user_id')->count();

        //提币总额
        $r_with_sum = UsersWalletOut::whereBetween('create_time',[$starttime,$endtime])->sum('number');

        //实际提现总额：
        $r_with_true = UsersWalletOut::whereBetween('create_time',[$starttime,$endtime])->where(['status'=>'2'])->sum('real_number');

        //总客损  == 实际充值金额
        $r_all_loss= $r_recharge_true;

        //用户总余额； //计算总额，再乘以汇率；


//        $legal_wallet['balance'] = UsersWallet::whereHas('currencyCoin', function ($query){
//                     $query->where("is_legal", 1);
//                })->get(['id', 'currency', 'legal_balance', 'lock_legal_balance'])
//                ->toArray();
//
//
//        foreach ($legal_wallet['balance'] as $k => $v) {
//            $num = $v['legal_balance'] + $v['lock_legal_balance'];
//            //$legal_wallet['totle'] += $num * $v['cny_price'];
//            $legal_wallet['usdt_totle'] += $num * $v['usdt_price'];
//        }

          $currencyModel = new Currency();
          $list =  $currencyModel->select(['id','name','price'])->get()->toArray();

        $r_total_balance = 0;
          foreach ($list as $k=>$v){
              $legal_balance = DB::table('users_wallet')
                  ->where(['currency'=>$v['id']])
                  ->first( array(
                      \DB::raw('SUM(legal_balance) as legal_balance'),
                      \DB::raw('SUM(lock_legal_balance) as lock_legal_balance'),

                      \DB::raw('SUM(change_balance) as change_balance'),
                      \DB::raw('SUM(lock_change_balance) as lock_change_balance'),

                      \DB::raw('SUM(lever_balance) as lever_balance'),
                      \DB::raw('SUM(lock_lever_balance) as lock_lever_balance'),

                      \DB::raw('SUM(micro_balance) as micro_balance'),
                      \DB::raw('SUM(lock_micro_balance) as lock_micro_balance'),
                  ));

                $all_price = $legal_balance->legal_balance + $legal_balance->lock_legal_balance+
                            $legal_balance->change_balance + $legal_balance->lock_change_balance+
                            $legal_balance->lever_balance + $legal_balance->lock_lever_balance+
                            $legal_balance->micro_balance + $legal_balance->lock_micro_balance;
               $all_price = $all_price * $v['price'];
              $r_total_balance += $all_price;
          }

        DB::beginTransaction();
        try {
            if(!$get){
                $get =  $UsersRecordMark;
            }
            $get->r_date = $r_date;
            $get->r_register_count = $r_register_count;
            $get->r_active_count = $r_active_count;
            $get->r_order_count = $r_order_count_1 + $r_order_count_2 + $r_order_count_3;
            $get->r_order_money = $r_order_money_1 + $r_order_money_2 + $r_order_money_3;
            $get->r_order_charge = $r_order_charge_1 + $r_order_charge_2;
            $get->r_profit_loss = $r_profit_loss_1 + $r_profit_loss_2;
            $get->r_recharge_count = $r_recharge_count;
            $get->r_recharge_sum = $r_recharge_sum;
            $get->r_recharge_true = $r_recharge_true;

            $get->r_with_number = $r_with_number;
            $get->r_with_sum = $r_with_sum;
            $get ->r_with_true = $r_with_true;
            $get ->r_all_loss = $r_all_loss;
            $get->r_total_balance = $r_total_balance;

            $get->save();

            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage());
        }



    }


    public function login1()
    {
        return view('admin.login1');
    }

    public function index()
    {
        $admin_role = AdminRolePermission::where("role_id", session()->get('admin_role_id'))->get();
        $admin_role_data = array();
        foreach ($admin_role as $r) {
            array_push($admin_role_data, $r->action);
        }
        return view('admin.indexnew')->with("admin_role_data", $admin_role_data);;
    }

    public function indexnew()
    {
        $admin_role = AdminRolePermission::where("role_id", session()->get('admin_role_id'))->get();
        $admin_role_data = array();
        foreach ($admin_role as $r) {
            array_push($admin_role_data, $r->action);
        }
        return view('admin.index')->with("admin_role_data", $admin_role_data);;
    }



    public function getVerificationCode(Request $request)
    {
        $http_client = app('LbxChainServer');

        $uri = '/v3/wallet/verification';

        $response = $http_client->request('post', $uri, [
            'form_params' => [
                'projectname' => config('app.name'),
            ],
        ]);
        $result = json_decode($response->getBody()->getContents(), true); 
        if (isset($result['code']) && $result['code'] == 0) {
            return $this->success('发送成功');
        } else {
            return $this->error($result['msg']);
        }
    }
}
