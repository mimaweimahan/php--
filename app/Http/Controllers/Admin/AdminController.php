<?php

namespace App\Http\Controllers\Admin;


use App\Admin;
use App\AdminRole;
use App\Agent;
use App\Users;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{

    public function users()
    {
        if (session()->get('admin_is_super') != '1') {
            abort(403);
        }
        $adminuser = Admin::all();
        $count = $adminuser->count();
        return response()->json(['code' => 0, 'count' => $count, 'msg' => '', 'data' => $adminuser]);
    }

    public function start()
    {
        $menu = [];
        $menu['code'] = 1;
        $menu['message'] = '成功';
        $data = [];
        $data[] = [
            'title' => '代理后台',
            'pageURL' => '/agent',
            'name' => '代理后台',
            'icon' => 'fa-user',
            'openType' => 0,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 1
        ];
        $data[] = [
            'title' => '基础设置',
            'pageURL' => '/admin/setting/index',
            'name' => '基础设置',
            'icon' => 'fa-cog',
            'openType' => 0,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 2
        ];
//        $data[] = [
//            'title' => '基础设置',
//            'pageURL' => '/admin/setting/index',
//            'name' => '基础设置',
//            'icon' => 'fa-cog',
//            'openType' => 0,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 3
//        ];
//        $data[] = [
//            'title' => '基础设置',
//            'pageURL' => '/admin/setting/index',
//            'name' => '基础设置',
//            'icon' => 'fa-cog',
//            'openType' => 0,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 4
//        ];

        $menu['data'] = $data;
        return $menu;
    }
    public function menu()
    {
        $menu = [];
        $menu['code'] = 1;
        $menu['message'] = '成功';
        $data = [];
//        $data[] = [
//            'title' => '主题',
//            'pageURL' => 'theme',
//            'name' => '主题',
//            'icon' => 'icon-pifuzhuti-kuai',
//            'openType' => 1,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 1
//        ];
        $data[] = [
            'title' => '基础设置',
            'pageURL' => '/admin/setting/index',
            'name' => '基础设置',
            'icon' => 'icon-shezhi',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 2
        ];
        $data[] = [
            'title' => '角色管理',
            'pageURL' => '/admin/manager/manager_roles',
            'name' => '角色管理',
            'icon' => 'icon-tubiaozhizuomoban-copy-copy',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 3
        ];
        $data[] = [
            'title' => '后台管理员',
            'pageURL' => '/admin/manager/manager_index',
            'name' => '后台管理员',
            'icon' => 'icon-guanliyuan',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 4
        ];
        $data[] = [
            'title' => '用户管理',
            'pageURL' => '/admin/user/user_index',
            'name' => '用户管理',
            'icon' => 'icon-guanliyuan-yonghuchaxun',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 5
        ];
        $data[] = [
            'title' => '实名认证管理',
            'pageURL' => '/admin/user/real_index',
            'name' => '实名认证管理',
            'icon' => 'icon-shimingrenzheng1',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 6
        ];
        $data[] = [
            'title' => '高级认证',
            'pageURL' => '/admin/user/hreal_index',
            'name' => '高级认证',
            'icon' => 'icon-shimingrenzheng1',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 6
        ];
        $data[] = [
            'title' => '新闻管理',
            'pageURL' => '/admin/news_index',
            'name' => '新闻管理',
            'icon' => 'icon-xinwenguanli1',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 8
        ];
        $data[] = [
            'title' => '投诉建议',
            'pageURL' => '/admin/feedback/index',
            'name' => '投诉建议',
            'icon' => 'icon-tousujianyi',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 8
        ];
        $data[] = [
            'title' => '日志信息',
            'pageURL' => '/admin/account/account_index',
            'name' => '日志信息',
            'icon' => 'icon-rizhi',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 9
        ];
//        $data[] = [
//            'title' => '商家管理',
//            'pageURL' => '/admin/seller',
//            'name' => '商家管理',
//            'icon' => 'icon-shangjia11',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
//        $data[] = [
//            'title' => '法币交易需求',
//            'pageURL' => '/admin/legal',
//            'name' => '法币交易需求',
//            'icon' => 'icon-fabijiaoyi1',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
//        $data[] = [
//            'title' => '法币交易信息',
//            'pageURL' => '/admin/legal_deal',
//            'name' => '法币交易信息',
//            'icon' => 'icon-fabijiaoyi2',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
        $data[] = [
            'title' => '币种管理',
            'pageURL' => '/admin/currency',
            'name' => '币种管理',
            'icon' => 'icon-currencyManagement',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '提币列表',
            'pageURL' => '/admin/cashb',
            'name' => '提币列表',
            'icon' => 'icon-tibi2',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '用户风险率汇总',
            'pageURL' => '/admin/hazard/total',
            'name' => '用户风险率汇总',
            'icon' => 'icon-fengxian',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '杠杆交易',
            'pageURL' => '/admin/Leverdeals/Leverdeals_show',
            'name' => '杠杆交易',
            'icon' => 'icon-heyueguanli-dianliang',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '会员关系图',
            'pageURL' => '/admin/invite/childs',
            'name' => '会员关系图',
            'icon' => 'icon-guanxitu1',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '秒合约交易',
            'pageURL' => '/admin/micro_order',
            'name' => '秒合约交易',
            'icon' => 'icon-miaobiao',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '币币交易挂单',
            'pageURL' => '/admin/exchange/index',
            'name' => '币币交易挂单',
            'icon' => 'icon-bibijiaoyi',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '已完成币币交易',
            'pageURL' => '/admin/exchange/complete_index',
            'name' => '已完成币币交易',
            'icon' => 'icon-yiwancheng',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '币币机器人',
            'pageURL' => '/admin/robote/list',
            'name' => '币币机器人',
            'icon' => 'icon-jiqiren',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '钱包管理',
            'pageURL' => '/admin/wallet/index',
            'name' => '钱包管理',
            'icon' => 'icon-qianbao2',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
//        $data[] = [
//            'title' => '新法币商家管理',
//            'pageURL' => '/admin/legal/store',
//            'name' => '新法币商家管理',
//            'icon' => 'icon-shangjia',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 11
//        ];
//        $data[] = [
//            'title' => '新法币订单管理',
//            'pageURL' => '/admin/legal/order',
//            'name' => '新法币订单管理',
//            'icon' => 'icon-dingdan1',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 11
//        ];
        $data[] = [
            'title' => '充币记录',
            'pageURL' => '/admin/user/charge_req',
            'name' => '充币记录',
            'icon' => 'icon-chongzhiliuliang',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '平台币走势',
            'pageURL' => '/admin/user/quotation',
            'name' => '平台币走势',
            'icon' => 'icon-pingtai3',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '已添加平台币行情',
            'pageURL' => '/admin/myquotation/all',
            'name' => '已添加平台币行情',
            'icon' => 'icon-hangqing-xuanzhong1',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '平台币更新器',
            'pageURL' => '/admin/robot/list',
            'name' => '平台币更新器',
            'icon' => 'icon-cloud_computing_',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        // $data[] = [
        //     'title' => '持币生息管理',
        //     'pageURL' => '/admin/wealth/product',
        //     'name' => '持币生息管理',
        //     'icon' => 'icon-suocang',
        //     'openType' => 2,
        //     'maxOpen' => -1,
        //     'extend' => false,
        //     'childs' => null,
        //     'id' => 11
        // ];
        // $data[] = [
        //     'title' => '持币生息交易',
        //     'pageURL' => '/admin/wealth/trade',
        //     'name' => '持币生息交易',
        //     'icon' => 'icon-suocangwakuang',
        //     'openType' => 2,
        //     'maxOpen' => -1,
        //     'extend' => false,
        //     'childs' => null,
        //     'id' => 11
        // ];
        $data[] = [
            'title' => '搬砖套利管理',
            'pageURL' => '/admin/bztl/product',
            'name' => '搬砖套利管理',
            'icon' => 'icon-suocang',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '搬砖套利交易',
            'pageURL' => '/admin/bztl/trade',
            'name' => '搬砖套利交易',
            'icon' => 'icon-suocangwakuang',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        // $data[] = [
        //     'title' => '日常盈利报表',
        //     'pageURL' => '/admin/statistic/index',
        //     'name' => '日常盈利报表',
        //     'icon' => 'icon-suocangwakuang',
        //     'openType' => 2,
        //     'maxOpen' => -1,
        //     'extend' => false,
        //     'childs' => null,
        //     'id' => 11
        // ];
        for ($i = 0, $j = count($data); $i < $j; $i++) {
            $data[$i]['id'] = $i + 1;
        }

        $menu['data'] = $data;
        return $menu;
    }
    public function menu1()
    {
        $menu = [];
        $menu['code'] = 1;
        $menu['message'] = '成功';
        $data = [];
        $data[] = [
            'title' => '主题',
            'pageURL' => 'theme',
            'name' => '主题',
            'icon' => 'icon-pifuzhuti-kuai',
            'openType' => 1,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 1
        ];
        $data[] = [
            'title' => '基础设置',
            'pageURL' => '/admin/setting/index',
            'name' => '基础设置',
            'icon' => 'fa-wrench',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 2
        ];
        $data[] = [
            'title' => '角色管理',
            'pageURL' => '/admin/manager/manager_roles',
            'name' => '角色管理',
            'icon' => 'fa-graduation-cap',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 3
        ];
        $data[] = [
            'title' => '后台管理员',
            'pageURL' => '/admin/manager/manager_index',
            'name' => '后台管理员',
            'icon' => 'fa-user-circle-o',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 4
        ];
        $data[] = [
            'title' => '用户管理',
            'pageURL' => '/admin/user/user_index',
            'name' => '用户管理',
            'icon' => 'fa-users',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 5
        ];
        $data[] = [
            'title' => '实名认证管理',
            'pageURL' => '/admin/user/real_index',
            'name' => '实名认证管理',
            'icon' => 'fa-check-square',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 6
        ];
        $data[] = [
            'title' => '新闻管理',
            'pageURL' => '/admin/news_index',
            'name' => '新闻管理',
            'icon' => 'fa-newspaper-o',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 8
        ];
        $data[] = [
            'title' => '投诉建议',
            'pageURL' => '/admin/feedback/index',
            'name' => '投诉建议',
            'icon' => 'fa-volume-control-phone',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 8
        ];
        $data[] = [
            'title' => '日志信息',
            'pageURL' => '/admin/account/account_index',
            'name' => '日志信息',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 9
        ];
//        $data[] = [
//            'title' => '商家管理',
//            'pageURL' => '/admin/seller',
//            'name' => '商家管理',
//            'icon' => 'fa-university',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
//        $data[] = [
//            'title' => '法币交易需求',
//            'pageURL' => '/admin/legal',
//            'name' => '法币交易需求',
//            'icon' => 'fa-gg-circle',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
//        $data[] = [
//            'title' => '法币交易信息',
//            'pageURL' => '/admin/legal_deal',
//            'name' => '法币交易信息',
//            'icon' => 'fa-dollar',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 10
//        ];
        $data[] = [
            'title' => '币种管理',
            'pageURL' => '/admin/currency',
            'name' => '币种管理',
            'icon' => 'fa-btc',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '提币列表',
            'pageURL' => '/admin/cashb',
            'name' => '提币列表',
            'icon' => 'fa-credit-card',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
//        $data[] = [
//            'title' => '用户风险率汇总',
//            'pageURL' => '/admin/hazard/total',
//            'name' => '用户风险率汇总',
//            'icon' => 'fa-dollar',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 11
//        ];
        $data[] = [
            'title' => '杠杆交易',
            'pageURL' => '/admin/Leverdeals/Leverdeals_show',
            'name' => '杠杆交易',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '会员关系图',
            'pageURL' => '/admin/invite/childs',
            'name' => '会员关系图',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '秒合约交易',
            'pageURL' => '/admin/micro_order',
            'name' => '秒合约交易',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '币币交易挂单',
            'pageURL' => '/admin/exchange/index',
            'name' => '币币交易挂单',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '已完成币币交易',
            'pageURL' => '/admin/exchange/complete_index',
            'name' => '已完成币币交易',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '币币机器人',
            'pageURL' => '/admin/robot/list',
            'name' => '币币机器人',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '钱包管理',
            'pageURL' => '/admin/wallet/index',
            'name' => '钱包管理',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
//        $data[] = [
//            'title' => '新法币商家管理',
//            'pageURL' => '/admin/legal/store',
//            'name' => '新法币商家管理',
//            'icon' => 'fa-list',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 11
//        ];
//        $data[] = [
//            'title' => '新法币订单管理',
//            'pageURL' => '/admin/legal/order',
//            'name' => '新法币订单管理',
//            'icon' => 'fa-list',
//            'openType' => 2,
//            'maxOpen' => -1,
//            'extend' => false,
//            'childs' => null,
//            'id' => 11
//        ];
        $data[] = [
            'title' => '充币记录',
            'pageURL' => '/admin/user/charge_req',
            'name' => '充币记录',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '平台币行情走势',
            'pageURL' => '/admin/user/quotation',
            'name' => '平台币行情走势',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '已添加平台币行情',
            'pageURL' => '/admin/myquotation/all',
            'name' => '已添加平台币行情',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '平台币更新器',
            'pageURL' => '/admin/robot/list',
            'name' => '平台币更新器',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '持币生息管理',
            'pageURL' => '/admin/wealth/product',
            'name' => '持币生息管理',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];
        $data[] = [
            'title' => '持币生息交易',
            'pageURL' => '/admin/wealth/trade',
            'name' => '持币生息交易',
            'icon' => 'fa-list',
            'openType' => 2,
            'maxOpen' => -1,
            'extend' => false,
            'childs' => null,
            'id' => 11
        ];

        for ($i = 0, $j = count($data); $i < $j; $i++) {
            $data[$i]['id'] = $i + 1;
        }

        $menu['data'] = $data;
        return $menu;
    }

    public function add()
    {
        if (session()->get('admin_is_super') != '1') {
            abort(403);
        }
        $id = Input::get('id', null);
        if (empty($id)) {
            $adminUser = new Admin();
        } else {
            $adminUser = Admin::find($id);
            if ($adminUser == null) {
                abort(404);
            }
        }
        $roles = AdminRole::all();
        return view('admin.manager.add', ['admin_user' => $adminUser, 'roles' => $roles]);
    }

    public function postAdd(Request $request)
    {
        if (session()->get('admin_is_super') != '1') {
            abort(403);
        }
        $id = Input::get('id', null);
        $validator = Validator::make(Input::all(), [
            'username' => 'required',
            'role_id' => 'required|numeric'
        ], [
            'username.required' => '姓名必须填写',
            'role_id.required' => '角色必须选择',
            'role_id.numeric' => '角色必须为数字'
        ]);
        if (empty($id)) {
            $adminUser = new Admin();
        } else {
            $adminUser = Admin::find($id);
            if ($adminUser == null) {
                return redirect()->back();
            }
        }
        $password = Input::get('password', '');
        $adminUser->role_id = Input::get('role_id', '0');
        if (Input::get('password', '') != '') {
            $adminUser->password = Users::MakePassword($password);
        }
        $validator->after(function ($validator) use ($adminUser, $id) {
            if (empty($id)) {
                if (Admin::where('username', Input::get('username'))->exists()) {
                    $validator->errors()->add('username', '用户已经存在');
                }
            }
        });

        $adminUser->username = Input::get('username', '');
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        try {
            $adminUser->save();
        } catch (\Exception $ex) {
            $validator->errors()->add('error', $ex->getMessage());
            return $this->error($validator->errors()->first());
        }
        return $this->success('添加成功');
    }

    public function del()
    {
        $admin = Admin::find(Input::get('id'));
        if ($admin == null) {
            abort(404);
        }
        $bool = $admin->delete();
        if ($bool) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function agent()
    {

        $admin = Agent::where('is_admin', 1)->where('level', 0)->first();

        if ($admin != null) {
            return redirect(route('agent'));
        } else {
            $hkok = DB::table('admin')->where('id', 1)->first();

            if ($hkok != null) {
                $insertData = [];
                $insertData['user_id'] = $hkok->id;
                $insertData['username'] = $hkok->username;
                $insertData['password'] = $hkok->password;
                $insertData['level'] = 0;
                $insertData['is_admin'] = 1;
                $insertData['reg_time'] = time();
                $insertData['pro_loss'] = 100.00;
                $insertData['pro_ser'] = 100.00;

                $id = DB::table('agent')->insertGetId($insertData);

                if ($id > 0) {
                    return redirect(route('agent'));
                } else {
                    return $this->error('失败');
                }
            }
        }
    }


}

?>
