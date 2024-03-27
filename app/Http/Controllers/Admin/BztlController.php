<?php

namespace App\Http\Controllers\Admin;


use App\Currency;
use App\Level;
use App\BztlProduct;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;


class BztlController extends Controller
{
    public function index(){
        return view('admin.bztl.index');
    }


    public function wealthLists(Request $request){
        $limit = $request->get('limit', 10);
        $result = new BztlProduct();
        $result = $result->orderBy('id', 'desc')->paginate($limit);

        return $this->layuiData($result);
    }

    public function add(Request $request){
        $currencies = Currency::where('is_display', 1)->orderBy('id', 'desc')->get();
        return view('admin.bztl.add',['currencies'=>$currencies]);
    }

    public function doadd(Request $request){
        $wealthName = $request->get("wealth_name",'');
        if(empty($wealthName)){
            return $this->error("请输入产品名");
        }
        $period = $request->get("period",0);
        if($period <=0){
            return $this->error("请输入期限");
        }
        $minDailyReturnRate = $request->get("min_daily_return_rate",0);
        if($minDailyReturnRate <=0){
            return $this->error("请输入最少收益");
        }
        $maxDailyReturnRate = $request->get("max_daily_return_rate",0);

        if($maxDailyReturnRate <=0){
            return $this->error("请输入最大收益");
        }

        $maxSingleLimit = $request->get("max_single_limit",0);
        if($maxSingleLimit <=0){
            return $this->error("请输入单日最大限额");
        }

        $minSingleLimit = $request->get("min_single_limit",0);
        if($minSingleLimit <=0){
            return $this->error("请输入单日最小限额");
        }


        $reneged = $request->get("reneged",0);
        if($reneged <=0){
            return $this->error("请输入违约金比例");
        }

        $objWealthProduct = new BztlProduct();
        $objWealthProduct->wealth_name = $wealthName;
        $objWealthProduct->period = $period;
        $objWealthProduct->max_daily_return_rate = $maxDailyReturnRate;
        $objWealthProduct->min_daily_return_rate = $minDailyReturnRate;
        $objWealthProduct->max_single_limit = $maxSingleLimit;
        $objWealthProduct->min_single_limit = $minSingleLimit;
        $objWealthProduct->reneged = $reneged;
        $objWealthProduct->create_time = time();
        $objWealthProduct->currency = $request->get('currency');
        $objWealthProduct->nlimit = $request->get('nlimit');
        $result = $objWealthProduct->save();
        if(!$result){
            return $this->error("保存失败");
        }

        return $this->success("保存成功");
    }

    public function edit(Request $request){
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }

        $result = new BztlProduct();
        $result = $result->find($id);
        $currencies = Currency::where('is_display', 1)->orderBy('id', 'desc')->get();

        return view("admin.bztl.edit",['result'=>$result,'currencies'=>$currencies]);
    }

    public function doedit(Request $request){
        $id = $request->get('id', 0);

        $wealthName = $request->get("wealth_name",'');
        if(empty($wealthName)){
            return $this->error("请输入产品名");
        }
        $period = $request->get("period",0);
        if($period <=0){
            return $this->error("请输入期限");
        }
        $minDailyReturnRate = $request->get("min_daily_return_rate",0);
        if($minDailyReturnRate <=0){
            return $this->error("请输入最少收益");
        }
        $maxDailyReturnRate = $request->get("max_daily_return_rate",0);

        if($maxDailyReturnRate <=0){
            return $this->error("请输入最大收益");
        }


        $maxSingleLimit = $request->get("max_single_limit",0);
        if($maxSingleLimit <=0){
            return $this->error("请输入单日最大限额");
        }

        $minSingleLimit = $request->get("min_single_limit",0);
        if($minSingleLimit <=0){
            return $this->error("请输入单日最小限额");
        }


        $reneged = $request->get("reneged",0);
        if($reneged <=0){
            return $this->error("请输入违约金比例");
        }
        $wealth = BztlProduct::find($id);
        $wealth->wealth_name = $wealthName;
        $wealth->period = $period;
        $wealth->max_daily_return_rate = $maxDailyReturnRate;
        $wealth->min_daily_return_rate = $minDailyReturnRate;
        $wealth->max_single_limit = $maxSingleLimit;
        $wealth->min_single_limit = $minSingleLimit;
        $wealth->reneged = $reneged;
        $wealth->currency = $request->get('currency');
        $wealth->nlimit = $request->get('nlimit');
        $result = $wealth->save();
        if(!$result){
            return $this->error("修改失败");
        }

        return $this->success("修改成功");
    }

    /**
     * 产品不会真的删除 只会改变状态
     * @param Request $request
     */
    public function del(Request $request){
        $id = $request->get('id', 0);

        $wealth = BztlProduct::find($id);
        if(empty($wealth)){
            $this->error("产品不存在");
        }
        if($wealth->status == BztlProduct::STATUS0){
            $wealth->status = BztlProduct::STATUS1;
        }else{
            $wealth->status = BztlProduct::STATUS0;
        }

        $rel = $wealth->save();
        if(!$rel){
            return $this->error("下架失败");
        }

        return $this->success("下架成功");

    }
}
