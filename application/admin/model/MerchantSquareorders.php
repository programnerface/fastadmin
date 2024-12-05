<?php

namespace app\admin\model;

use think\Model;


class MerchantSquareorders extends Model
{

    

    

    // 表名
    protected $name = 'merchant_squareorders';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'order_status_text'
    ];
    

    
    public function getOrderStatusList()
    {
        return ['未确认' => __('未确认'), '已入账' => __('已入账'), '已发货' => __('已发货'), '已退款' => __('已退款')];
    }


    public function getOrderStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['order_status']) ? $data['order_status'] : '');
        $list = $this->getOrderStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
