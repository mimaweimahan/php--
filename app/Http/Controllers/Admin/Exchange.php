<?php

namespace App\Http\Controllers\Admin;

use App\AccountLog;
use App\Currency;
use App\CurrencyMatch;
use App\Transaction;
use App\TransactionComplete;
use App\TransactionIn;
use App\TransactionOut;
use App\Users;
use App\UsersWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Needle as NeedleModel;
use Illuminate\Support\Facades\DB;
use think\Exception;

class Exchange extends Controller
{
    //
    public function index()
    {
        $currency_type = Currency::all();
        return view('admin.exchange.index', [
            'currency_type' => $currency_type
        ]);
    }

    public function completeIndex()
    {
        $currency_type = Currency::all();
        return view('admin.exchange.index1', [
            'currency_type' => $currency_type
        ]);
    }

    public function completeDelete(Request $request)
    {
        $exis = TransactionComplete::find($request->get('id'));

        if (is_null($exis)) {
            $res = 1;
        } else {
            $res = $exis->delete();
        }
        return $res ? ['ok' => 1] : ['ok' => -1];
    }

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $start_time = strtotime($request->get('start_time', 0));
        $end_time = strtotime($request->get('end_time', 0));
        $currency = $request->get('currency_type', 0);
        $sign = $request->get('sign', 0);//正负号，0所有，1，正，-1，负号

        $trade_type = $request->get('trade_type', 1);
        switch ($trade_type) {
            case "1":
                $list = new TransactionIn();
                break;
            case "2":
                $list = new TransactionOut();
                break;
        }


        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }

        if (!empty($start_time)) {
            $list = $list->where('create_time', '>=', $start_time);
        }
        if (!empty($end_time)) {
            $list = $list->where('create_time', '<=', $end_time);
        }

        if (!empty($account)) {
            $list = $list->whereHas('user', function ($query) use ($account) {
                $query->where("phone", 'like', '%' . $account . '%')->orwhere('email', 'like', '%' . $account . '%');
            });
        }

        $list = $list->orderBy('id', 'desc')->paginate($limit);

        $a = $list->items();
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function completeList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $start_time = strtotime($request->get('start_time', 0));
        $end_time = strtotime($request->get('end_time', 0));
        $currency = $request->get('currency_type', 0);
        $sign = $request->get('sign', 0);//正负号，0所有，1，正，-1，负号

        $trade_type = $request->get('trade_type', 1);
        $list = new TransactionComplete();


        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }

        if (!empty($start_time)) {
            $list = $list->where('create_time', '>=', $start_time);
        }
        if (!empty($end_time)) {
            $list = $list->where('create_time', '<=', $end_time);
        }

        if (!empty($account)) {
            $list1 = Users::where("phone", 'like', '%' . $account . '%')
                ->orwhere('email', 'like', '%' . $account . '%')
                ->orWhere('account_number', 'like', '%' . $account . '%')->pluck('id');
            $user_list = $list1->toArray();
           $list = $list->whereIn('user_id',$user_list)->orWhere(function ($query)use($user_list) {
               $query->whereIn('from_user_id', $user_list);
           });
        }

        $list = $list->orderBy('id', 'desc')->paginate($limit);
        //dd($list->items());
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function revoke(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $entry = $type == 'buy' ? TransactionIn::find($id) : TransactionOut::find($id);
        $user = Users::find($entry->user_id);
        $match = CurrencyMatch::where('currency_id', $entry->currency)->where('legal_id', $entry->legal)->first();

        if (is_null($match) || is_null($user) || is_null($entry)) {
            return ['code' => -1];
        }

        if ($type == 'buy') {
            $amount = bc_mul($entry->number, $entry->price, 5);

            DB::beginTransaction();
            try {
                $user_currency = UsersWallet::where("user_id", $user->id)
                    ->where("currency", $entry->legal)
                    ->lockForUpdate()
                    ->first();

                if (bc_comp($user_currency->lock_change_balance, $amount) < 0) {
                    throw new \Exception('非法操作');
                }

                $data_wallet1 = [
                    'balance_type' => 2,
                    'wallet_id' => $user_currency->id,
                    'lock_type' => 1,
                    'create_time' => time(),
                    'before' => $user_currency->lock_change_balance,
                    'change' => -($amount + $entry->fee),
                    'after' => bc_sub($user_currency->lock_change_balance, $amount + $entry->fee, 5),
                ];
                $data_wallet2 = [
                    'balance_type' => 2,
                    'wallet_id' => $user_currency->id,
                    'lock_type' => 0,
                    'create_time' => time(),
                    'before' => $user_currency->change_balance,
                    'change' => $amount + $entry->fee,
                    'after' => bc_add($user_currency->change_balance, $amount + $entry->fee, 5),
                ];

                $user_currency->lock_change_balance = bc_sub($user_currency->lock_change_balance, $amount + $entry->fee, 5);
                $user_currency->change_balance = bc_add($user_currency->change_balance, $amount + $entry->fee, 5);
                $user_currency->save();//法币余额增加 法币锁定余额减少
                $del_result = $entry::destroy($id);
                if ($del_result < 1) {
                    throw new \Exception('取销买入交易失败');
                }
                AccountLog::insertLog([
                    'user_id' => $user->id,
                    'value' => -($amount + $entry->fee),
                    'info' => "取消买入交易，解除余额锁定",
                    'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                    'currency' => $entry->legal,
                ], $data_wallet1);
                AccountLog::insertLog([
                    'user_id' => $user->id,
                    'value' => $amount + $entry->fee,
                    'info' => "取消买入交易,余额退回",
                    'type' => AccountLog::TRANSACTIONIN_IN_DEL,
                    'currency' => $entry->legal,
                ], $data_wallet2);
                DB::commit();
                return ['code' => 1];
            } catch (\Exception $exception) {
                DB::rollBack();
                return ['code' => -1, 'message' => $exception->getMessage().$exception->getLine()];
            }

        } else {
            DB::beginTransaction();
            try {
                $user_wallet = UsersWallet::where('user_id', $user->id)
                    ->where('currency', $entry->currency)
                    ->lockForUpdate()
                    ->first();

                if (bc_comp($user_wallet->lock_change_balance, $entry->number) < 0) {
                    throw new \Exception('非法操作');
                }

                $data_wallet1 = [
                    'balance_type' =>  2,
                    'wallet_id' => $user_wallet->id,
                    'lock_type' => 1,
                    'create_time' => time(),
                    'before' => $user_wallet->lock_change_balance,
                    'change' => -$entry->number,
                    'after' => bc_sub($user_wallet->lock_change_balance, $entry->number, 5),
                ];
                $data_wallet2 = [
                    'balance_type' =>  2,
                    'wallet_id' => $user_wallet->id,
                    'lock_type' => 0,
                    'create_time' => time(),
                    'before' => $user_wallet->change_balance,
                    'change' => $entry->number,
                    'after' => bc_add($user_wallet->change_balance, $entry->number, 5),
                ];
                $user_wallet->lock_change_balance = bc_sub($user_wallet->lock_change_balance, $entry->number, 5);
                $user_wallet->change_balance = bc_add($user_wallet->change_balance, $entry->number, 5);
                $user_wallet->save();//余额增加 法币锁定余额减少
                $del_result = $entry::destroy($id);
                if ($del_result < 1) {
                    throw new \Exception('取销卖出交易失败');
                }
                AccountLog::insertLog([
                    'user_id' => $user->id,
                    'value' => -$entry->number,
                    'info' => "取消卖出交易,解除交易余额锁定",
                    'type' => AccountLog::TRANSACTIONIN_OUT_DEL,
                    'currency' => $entry->currency,
                ],$data_wallet1);
                AccountLog::insertLog([
                    'user_id' => $user->id,
                    'value' => $entry->number,
                    'info' => "取消卖出交易,退回交易",
                    'type' => AccountLog::TRANSACTIONIN_OUT_DEL,
                    'currency' => $entry->currency,
                ],$data_wallet2);

                DB::commit();

                return ['code' => 1];
            }
            catch (\Exception $exception)
            {
                DB::rollBack();
                return ['code' => -1, 'message' => $exception->getMessage()];
            }
        }

    }

    public function deal(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        return dealTrade($id,$type);
    }
}
