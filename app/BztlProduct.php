<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Seller;

class BztlProduct extends Model
{
    const STATUS0 = 0; //已下架
    const STATUS1 = 1; //已上架

    protected static $statusList = [
        '已下架',
        '已上架'
    ];


    protected $table = 'bztl_product';
    public $timestamps = false;

    protected $appends = [
        'daily_return_rate',
        'single_limit',
        'show_status',
        'currency_name',
    ];

    public function getDailyReturnRateAttribute(){
        return "{$this->attributes['min_daily_return_rate']} - {$this->attributes['max_daily_return_rate']}";
    }

    public function getSingleLimitAttribute(){
        return "{$this->attributes['min_single_limit']} - {$this->attributes['max_single_limit']}";
    }

    public function getShowStatusAttribute(){
        $value = isset(self::$statusList[$this->attributes['status']]) ? self::$statusList[$this->attributes['status']]:"未知状态";
        return $value;
    }

    public function getCurrencyNameAttribute()
    {
       return $this->hasOne('App\Currency', 'id', 'currency')->value('name') ?? '';

    }


}
