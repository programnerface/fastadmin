<?php

namespace app\admin\model;

use think\Model;


class Refund extends Model
{

    

    

    // 表名
    protected $name = 'refund';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'refund_status_text'
    ];
    

    
    public function getRefundStatusList()
    {
        return ['处理中' => __('处理中'), '退款失败' => __('退款失败'), '退款成功' => __('退款成功')];
    }


    public function getRefundStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refund_status']) ? $data['refund_status'] : '');
        $list = $this->getRefundStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
