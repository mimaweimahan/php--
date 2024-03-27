<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IeoRecord extends Model
{
    //
    protected $table = 'ieo_record';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['account_number'];

    public  function  setItimeAttribute($value)
    {
        $this->attributes['itime'] = strtotime($value);
    }

    public function getAccountNumberAttribute()
    {
        $value = $this->hasOne('App\Users', 'id', 'user_id')->value('account_number');
        //   $value = $this->getAttribute('phone');
//        var_dump($this);
//        die;
        if (empty($value)) {
            $value = $this->attributes['email']??'123@123.com';
            $n = strripos($value, '@');
            $value = mb_substr($value, 0, 2) . '******' . mb_substr($value, $n);
        } else {
            $value = mb_substr($value, 0, 3) . '******' . mb_substr($value, -3, 3);
        }
        return $value;
    }


    public function getCreatedAtAttribute()
    {
        $value = $this->attributes['created_at'];
        return $value ? date('Y-m-d H:i:s', $value ) : '';
    }

    public function getQueueableRelations()
    {
        // TODO: Implement getQueueableRelations() method.
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
}
