<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use App\DAO\PrizePool\CandySender;
use App\DAO\UserDAO;
use App\PrizePool;
use App\Setting;
use App\Users;
use App\UserRealHigh as UserReal;
use App\IdCardIdentity;
use App\Events\RealNameEvent;

class UserRealHighController extends Controller
{

    public function index()
    {
        return view("admin.huserReal.index");
    }

    //用户列表
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $review_status_s = $request->get('review_status_s', 0);

        $list = new UserReal();
        if (!empty($account)) {
            $list = $list->whereHas('user', function ($query) use ($account) {
                $query->where("phone", 'like', '%' . $account . '%')->orwhere('email', 'like', '%' . $account . '%');
            });
        }
        if(!empty($review_status_s)){
            $list = $list->where('review_status',$review_status_s);
        }

        $list = $list->orderBy('id', 'desc')->paginate($limit);
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function detail(Request $request)
    {

        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }

        $result = UserReal::find($id);

        return view('admin.huserReal.info', ['result' => $result]);
    }

    public function del(Request $request)
    {
        $id = $request->get('id');
        $userreal = UserReal::find($id);
        if (empty($userreal)) {
            $this->error("认证信息未找到");
        }
        try {

            $userreal->delete();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }


    public function auth(Request $request)
    {
        $id = $request->get('id', 0);
        $userreal = UserReal::find($id);
        if (empty($userreal)) {
            return $this->error('参数错误');
        }
        $user = Users::find($userreal->user_id);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if ($userreal->review_status == 1) {
            //从未认证到认证
            //查询users表判断是否为第一次实名认证
            $is_realname = $user->is_realname;
            if ($is_realname != 2) {
                //1:未实名认证过  2：实名认证过

                $user->is_realname = 2;

                $user->save();//自己实名认证获取通证结束
                //判断自己上级的的触发奖励
                //UserDAO::addCandyNumber($user);
            }
            $userreal->review_status = 2;
        } else if ($userreal->review_status == 2) {
            $userreal->review_status = 1;
        } else {
            $userreal->review_status = 1;
        }
        try {
            $userreal->save();
            //用户实名事件
            if ($userreal->review_status == 2) {
                event(new RealNameEvent($user, $userreal));
            }
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

}
