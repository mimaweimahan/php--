<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:39
 */

namespace App\Http\Controllers\Admin;


use App\Currency;
use App\CurrencyMatch;
use App\RobotExchange as Robot;
use App\RobotPlan;
use App\Users;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class RobotExchangeController extends Controller
{

    /**添加一个机器人
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        if (request()->isMethod('GET')) {

            $id = request()->input('id', 0);
            if (empty($id)) {
                $result = new Robot();
            } else {
                $result = Robot::find($id);
            }
            $currencies = Currency::where('is_display', 1)->where('is_legal', 0)->orderBy('id', 'desc')->get();
            $legals = Currency::where('is_display', 1)->where('is_legal', 1)->orderBy('id', 'desc')->get();
            $currencies = $currencies->toArray();
            $exists = $result->currency_ids;
            $exists_ar=explode(',',$exists);
            foreach ($currencies as $key => & $value) {
                $value['value'] = $value['id'];
                if(in_array($value['id'],$exists_ar))
                {
                    $value['selected']=true;
                }
            }
            return view('admin.robotexchange.add')->with(['currencies' => $currencies, 'legals' => $legals, 'result' => $result]);
        }

        if (request()->isMethod('POST')) {

            $data['currency_ids'] = request()->input('currency_ids', 0);
            $data['legal_id'] = request()->input('legal_id', 0);
            $data['max'] = request()->input('number_max', 0);
            $data['min'] = request()->input('number_min', 0);
            $data['second'] = request()->input('second', '');
            $data['mult'] = request()->input('mult', '');


            if (!is_numeric($data['max']) || !is_numeric($data['min'])) return $this->error('上下限只能是数字');

            $id = request()->input('id', 0);

            if ($id) {
                $robot = Robot::find($id);
            } else {
                $robot = new Robot();
                $robot->create_time = time();
            }

            $data['sell'] = request()->input('sell', 0);
            $data['buy'] = request()->input('buy', 0);

            DB::beginTransaction();
            try {
                $robot->mult = $data['mult'];
                $robot->currency_ids = $data['currency_ids'];
                $robot->legal_id = $data['legal_id'];
                $robot->max = $data['max'];
                $robot->min = $data['min'];
                $robot->second = $data['second'];
                $robot->sell = $data['sell'];
                $robot->buy = $data['buy'];

                $info = $robot->save();
                if (!$info) throw new \Exception('保存失败');

                DB::commit();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            }
        }
    }

    public function scheAdd()
    {
        if (request()->isMethod('GET')) {

            $id = request()->input('id', 0);
            if (empty($id)) {
                $result = new Robot();
            } else {
                $result = Robot::find($id);
            }
            $currencies = CurrencyMatch::where('market_from', 3)->orderBy('id', 'desc')->get();
            $legals = Currency::where('is_display', 1)->where('is_legal', 1)->orderBy('id', 'desc')->get();

            return view('admin.robotexchange.scheadd')->with(['currencies' => $currencies, 'rid' => request()->get('rid'), 'legals' => $legals, 'result' => $result]);
        }

        if (request()->isMethod('POST')) {
            $data = request()->post();


            $id = request()->input('id', 0);

            if ($id) {
                $robot = RobotPlan::find($id);
            } else {
                $robot = new RobotPlan();
            }


            DB::beginTransaction();
            try {

                $robot->itime = strtotime($data['itime']);
                $robot->etime = strtotime($data['etime']);

                $robot->base = $data['base'] ?? '';
                $robot->target = $data['target'] ?? '';
                $robot->remark = $data['remark'] ?? '没有描述';
                $robot->float_down = $data['float_down'] ?? 0;
                $robot->float_up = $data['float_up'];
                $robot->max_price = $data['max_price'] ?? 0;
                $robot->min_price = $data['min_price'] ?? 0;
                $robot->rid = $data['rid'];

                $info = $robot->save();
                if (!$info) throw new \Exception('保存失败');

                DB::commit();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            }
        }
    }

    /**返回页面
     *
     */
    public function list()
    {
        return view('admin.robotexchange.list');
    }

    public function sche()
    {
        return view('admin.robotexchange.schelist', [
            'rid' => request()->get('rid'),
        ]);
    }

    /**返回列表数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function listData()
    {
        $limit = request()->input('limit', 10);
        $list = Robot::paginate($limit);
        return $this->layuiData($list);
    }

    public function scheData()
    {
        $limit = request()->input('limit', 10);
        $list = RobotPlan::where('rid', request()->input('rid'))->paginate($limit);
        return $this->layuiData($list);
    }


    public function delete()
    {
        $id = request()->input('id', 0);
        $robot = Robot::find($id);

        if (!$robot) return $this->error('找不到这个机器人');

        if ($robot->status == Robot::START) return $this->error('机器人正在运行,不能删除');

        DB::beginTransaction();
        try {
            $info = $robot->delete();
            if (!$info) throw new \Exception('删除失败');

            DB::commit();
            return $this->success('删除成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    public function scheDelete()
    {
        $id = request()->input('id', 0);
        $robot = RobotPlan::find($id);

        if (!$robot) return $this->error('找不到这个机器人');

        DB::beginTransaction();
        try {
            $info = $robot->delete();
            if (!$info) throw new \Exception('删除失败');

            DB::commit();
            return $this->success('删除成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    public function start()
    {
        $id = request()->input('id', 0);
        $robot = Robot::find($id);
        $robot->status = $robot->status == Robot::START ? Robot::STOP : Robot::START;
        $robot->save();
    }


}
