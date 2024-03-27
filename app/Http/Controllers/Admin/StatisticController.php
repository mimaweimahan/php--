<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\{
    IdCardIdentit,
    UsersRecordMark,
    Wallet
};
use Illuminate\Support\Facades\DB;
class StatisticController extends Controller
{
    public function index(){
        return view('admin.statistic.index');
    }
    
    public function lists(Request $request)
    {
        $page = $request->input("page", 1);
        $limit = $request->input("limit", 20);
        $id = $request->input("id", '');
        $account_name = $request->input("account_name", '');
        $product = $request->input("product", 0);
        $status = $request->input("status", '');


        // $query =  new Users();
        // $list = $query->select(DB::raw('count(*) as num'),DB::raw("from_unixtime(time, '%Y-%m-%d') as date"))->groupBy(DB::raw("from_unixtime(time, '%Y-%m-%d')"))->orderBy('id', 'desc')->paginate($limit);
        $list = [];
        $count = 300;
        $limit = 20;
        $offset = ($page - 1 ) * $limit;
        $limits = $offset + $limit;
        for($i=$offset;$i<$limits;$i++){
            $list[] = [
                'date'=>date('Y-m-d',strtotime('-'.$i.' day')),
                'num'=>mt_rand(1,999),
                'active_user'=>mt_rand(1,50),
                'pay_user'=>mt_rand(1,20),
                'pay_num'=>mt_rand(300,600),
                'pay_all_num'=>mt_rand(300,1000),
                'pay_withdraw'=>mt_rand(1,200),
                'pay_win'=>mt_rand(1,200) * (time() % 2 > 0 ? 1 : -1),
                'buy_user_num'=>mt_rand(1,100),
                'buy_user_total'=>mt_rand(500,1999),
                'buy_true_count'=>mt_rand(100,1000),
                'withdraw_true_count'=>mt_rand(100,200),
                'withdraw_num'=>mt_rand(1,10),
                'withdraw_count'=>mt_rand(200,500),
                'total_lose'=>mt_rand(200,400),
                'user_total_count'=>mt_rand(2000,10000)
            ];
        }
        $return = ['code'=>0,'count'=>$count,'data'=>$list];
        return json_encode($return);
        // return $this->layuiData($list);
    }

    public function lists2(Request $request)
    {
        $page = $request->input("page", 1);
        $limit = $request->input("limit", 20);
        $start_time = ($request->get('start_time', 0));
        $end_time = ($request->get('end_time', 0));

        $list = new UsersRecordMark();

        if (!empty($start_time)) {
            $list = $list->where('r_date', '>=', $start_time);
        }
        if (!empty($end_time)) {
            $list = $list->where('r_date', '<=', $end_time);
        }
        $list = $list->orderBy('id', 'desc')->paginate($limit);

//        $return = ['code'=>0,'count'=>$list->total(),'data'=>$list->items()];
//        return json_encode($return);

        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);

    }

}