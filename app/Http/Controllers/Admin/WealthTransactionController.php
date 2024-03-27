<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\WealthProduct;
use App\WealthTransaction;
use Illuminate\Http\Request;


class WealthTransactionController extends Controller
{
    public function index(){
        $product = WealthProduct::where('status',WealthProduct::STATUS1)->get();
        return view("admin.wealth.trade.index",['product'=>$product]);
    }



    public function lists(Request $request){
        $limit = $request->input("limit", 20);
        $id = $request->input("id", '');
        $account_name = $request->input("account_name", '');
        $product = $request->input("product", 0);
        $status = $request->input("status", '');


        $query = WealthTransaction::whereHas('user', function ($query) use ($account_name) {
            $account_name != '' && $query->where('account_number', $account_name)->orWhere('phone', $account_name);

        })->whereHas('wealthProduct',function ($query) use($product){
            if(!empty($product)){
                $query->where('wealth_product_id',$product);
            }
        })->where(function ($query) use ($id,$product,$status){
            if(!empty($id)){
                $query ->where('id',$id);
            }
            if($status){
                $query->where('status',$status);
            }

        });

        $query = $query->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($query);
    }
}
