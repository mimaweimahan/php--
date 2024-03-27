<?php


namespace App\Http\Controllers\Api;


use App\CoinTrade;
use App\Currency;
use App\CurrencyMatch;
use App\Logic\CoinTradeLogic;
use App\Users;
use Illuminate\Http\Request;

class CoinTradeController extends Controller
{
   public function submit(Request $request){
       $legal = $request->input('legal_id');
       $currency = $request->input('currency_id');
       $target_price = $request->input('target_price');
       $type = $request->input('type');
       $amount = $request->input('amount');
       $match = CurrencyMatch::where([
           'legal_id' => $legal,
           'currency_id' => $currency,
           'open_coin_trade' => 1
       ])->first();
       if(!$match){
           return $this->error('找不到交易对');
       }
       if(!$legal || !$currency || $target_price< 0 || $amount<0){
           return $this->error('参数错误');
       }
       $userId = Users::getUserId();
       try{
           switch ($type){
               case 1:          //买
                   CoinTradeLogic::userBuyCoint($userId,$currency,$legal,$amount,$target_price);
                   break;
               case 2:          //卖
                   CoinTradeLogic::userSellCoin($userId,$currency,$legal,$amount,$target_price);
                   break;
               default:
                   return $this->error('参数错误');
           }

       }catch (\Exception $e){
           return $this->error($e->getMessage());
       }

       return $this->success('');
   }

   public function tradeList(Request $request){
       $limit = $request->get('limit', 20);
       $page = $request->get('page', 1);
       $user_id = Users::getUserId();
       $currency_id = $request->get('currency_id');
       $legal_id = $request->get('legal_id');
       $status = $request->get('status');
       $where = [];
       if($currency_id){
           $where['currency_id'] = $currency_id;
       }
       if($legal_id){
           $where['legal_id'] = $legal_id;
       }
       if($status){
           $where['status'] = $status;
       }

       $list = CoinTrade::where('u_id',$user_id)
           ->where($where)
           ->orderBy('id','desc')
           ->skip($limit*($page-1))->take($limit)->get();
       foreach($list as &$li){
           $li['symbol'] = Currency::getNameById($li->currency_id).'/'.Currency::getNameById($li->legal_id);
       }
       return $this->success($list);
   }

   public function cancelTrade(Request $request){
       $id = $request->get('id');
       $user_id = Users::getUserId();
       $tradeOrder = CoinTrade::find($id);
       if(!$tradeOrder){
           return $this->error('参数错误');
       }
       if($tradeOrder->u_id != $user_id){
           return $this->error('请求异常');
       }
       if($tradeOrder->status != 1){
           return $this->error('状态异常');
       }
        try{
            $res = CoinTradeLogic::cancelTrade($id);
        }catch (\Exception $e){
           return $this->error('取消失败:'.$e->getMessage());
        }
       return $this->success('取消成功');

   }
}
