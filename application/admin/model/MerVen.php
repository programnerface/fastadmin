<?php

namespace app\admin\model;

use think\Model;


class MerVen extends Model
{

    

    

    // 表名
    protected $name = 'mer_ven';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return ['Zelle' => __('Zelle'), 'Cash' => __('Cash'), 'Venmo' => __('Venmo'), 'Square' => __('Square')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('Admin', 'mer_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function admin1()
    {
        return $this->belongsTo('Admin', 'ven_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
