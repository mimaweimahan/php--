<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BztlTransactionLog extends Model
{
    protected $table = 'bztl_transaction_log';
    public $timestamps = false;
    
    protected $appends = [
        'show_status',
        'show_add_time',
        'user',
        'product',
        'show_end_time',
        'show_last_time'
    ];

    public function user()
    {
        return $this->belongsTo('App\Users', 'user_id', 'id')->withDefault();
    }

    public function bztlProduct(){
        return $this->belongsTo('App\BztlProduct', 'bztl_product_id', 'id')->withDefault();
    }
    public function getUserAttribute()
    {
        return Users::find($this->attributes['user_id'],['id','phone','account_number']);//->values('account_number,phone');
    }

    public function getProductAttribute()
    {
        return BztlProduct::find($this->attributes['bztl_product_id']);
    }

    public function getShowAddTimeAttribute(){
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getShowLastTimeAttribute()
    {
        $value = $this->attributes['calc_time'];

        return $value>0 ? date('Y-m-d H:i:s', $value) : '-';
    }

    /**
     * 添加结算日志
     */
    public static function addLog($data)
    {
        $time = time();
        $log = new BztlTransactionLog();
        $log->user_id = $data['uid'] ?? 0;
        $log->bztl_product_id = $data['id'];
        $log->caution_money = $data['cmoney'] ?? 0;
        $log->revenue = $data['revenue'] ?? 0;
        $log->rate = $data['rate'] ?? 0;
        $log->calc_time = $time;
        $log->create_time = $time;
        $result = $log->save();
    }

}
