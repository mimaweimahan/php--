<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:45
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class RobotExchange extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'robot_exchange';

    protected $appends = [
        'status_name',
        'create_date',
        'legal_info',
        'sell_status',
        'buy_status',
    ];

    const STOP = 0;
    const START = 1;

    const OPEN = 1;
    const CLOSE = 0;

    public function getSellStatusAttribute()
    {
        $value                 = $this->attributes['sell'];
        $status[static::OPEN]  = '开启';
        $status[static::CLOSE] = '关闭';
        return $status[$value];
    }

    public function getBuyStatusAttribute()
    {
        $value                 = $this->attributes['buy'];
        $status[static::OPEN]  = '开启';
        $status[static::CLOSE] = '关闭';
        return $status[$value];
    }

    public function getStatusNameAttribute()
    {
        $value                 = $this->attributes['status'];
        $status[static::STOP]  = '已停止';
        $status[static::START] = '已开启';
        return $status[$value];
    }

    public function getCreateDateAttribute()
    {
        $value = $this->attributes['create_time'];
        return date('Y-m-d H:i:s', $value);
    }


    public function getLegalInfoAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'legal_id')->value('name') ?? '';
    }

}
