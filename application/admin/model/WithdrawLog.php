<?php

namespace app\admin\model;

use think\Model;


class WithdrawLog extends Model
{

    

    

    // 表名
    protected $name = 'withdrawn_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'withdrawal_status_text',
        'withdrawal_check_text'
    ];
    

    
    public function getWithdrawalStatusList()
    {
        return ['已打款' => __('已打款'), '已到账' => __('已到账'), '处理中' => __('处理中')];
    }

    public function getWithdrawalCheckList()
    {
        return ['待审核' => __('待审核'), '审核通过' => __('审核通过')];
    }


    public function getWithdrawalStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['withdrawal_status']) ? $data['withdrawal_status'] : '');
        $list = $this->getWithdrawalStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getWithdrawalCheckTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['withdrawal_check']) ? $data['withdrawal_check'] : '');
        $list = $this->getWithdrawalCheckList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
