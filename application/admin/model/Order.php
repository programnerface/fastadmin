<?php

namespace app\admin\model;

use think\Model;


class Order extends Model
{

    

    

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'wallets_text',
        'order_state_text'
    ];
    

    
    public function getWalletsList()
    {
        return ['Zelle' => __('Zelle'), 'Square' => __('Square'), 'Cash App' => __('Cash App'), 'Venmo' => __('Venmo')];
    }

    public function getOrderStateList()
    {
        return ['Unconfirmed' => __('未确认'), 'Paid' => __('已入账'), 'Delivered' => __('已签收')];
    }


    public function getWalletsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['wallets']) ? $data['wallets'] : '');
        $list = $this->getWalletsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOrderStateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['order_state']) ? $data['order_state'] : '');
        $list = $this->getOrderStateList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
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
