<?php

namespace app\admin\model;

use think\Model;


class Cash extends Model
{

    

    

    // 表名
    protected $name = 'cash';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'order_status_text',
        'order_check_text'
    ];
    

    
    public function getOrderStatusList()
    {
        return ['未确认' => __('未确认'), '已入账' => __('已入账'), '已发货' => __('已发货')];
    }

    public function getOrderCheckList()
    {
        return ['待审核' => __('待审核'), '审核通过' => __('审核通过')];
    }


    public function getOrderStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['order_status']) ? $data['order_status'] : '');
        $list = $this->getOrderStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOrderCheckTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['order_check']) ? $data['order_check'] : '');
        $list = $this->getOrderCheckList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function type()
    {
        return $this->belongsTo('app\admin\model\product\Type', 'product_type_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function country()
    {
        return $this->belongsTo('Country', 'country_id', 'country_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function zone()
    {
        return $this->belongsTo('Zone', 'zone_id', 'zone_id', [], 'LEFT')->setEagerlyType(0);
    }
}
