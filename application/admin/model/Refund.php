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



    public function getWalletsList()
    {
        return ['Zelle' => __('Zelle'), 'Square' => __('Square'), 'Cash App' => __('Cash App'), 'Venmo' => __('Venmo')];
    }

    public function getRefundStatusList()
    {
        return ['待退款' => __('待退款'), '已退款' => __('已退款')];
    }


    public function getRefundStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refund_status']) ? $data['refund_status'] : '');
        $list = $this->getRefundStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getWalletsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['wallets']) ? $data['wallets'] : '');
        $list = $this->getWalletsList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
