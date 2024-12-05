<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use  think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Month extends Backend
{

    /**
     * MonthTrade模型对象
     * @var \app\admin\model\MonthTrade
     */
    protected $model = null;
    protected $dataLimit = 'personal';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\MonthTrade;

        $this->getMonthTrade();
    }

    public function getMonthTrade(){

       /* $query = Db::table('fa_day_trade_table')
            ->field([
                'DATE_FORMAT(order_date, "%Y-%m") as month', // 按 年-月 格式分组
                'SUM(payment_amount) as total_amount',       // 计算交易总额
                'SUM(order_count) as total_orders',         // 计算总订单数
            ])
            ->group('DATE_FORMAT(order_date, "%Y-%m")')    // 按月份分组
            ->select();*/
        $query = Db::table('fa_day_trade_table')
            ->field([
                'MAX(order_date) as latest_date',                 // 每月的最新日期
                'DATE_FORMAT(order_date, "%Y-%m") as month',      // 按 年-月 格式分组
                'SUM(payment_amount) as total_amount',            // 计算交易总额
                'SUM(order_count) as total_orders',               // 计算总订单数
            ])
            ->group('DATE_FORMAT(order_date, "%Y-%m")')          // 按月份分组
            ->select();


        foreach ($query as $data){

            $insert=$this->insertCheck($data['latest_date']);


            if ($insert == $data['latest_date']){

                $table=$this->model->getTable();
                Db::table($table)->insert([
                    'month'=>$data['latest_date'],
                    'payment_amount' =>$data['total_amount'],
                    'order_count' =>$data['total_orders'],
                ]);
            }else{
                $table=$this->model->getTable();
                foreach ($insert as $data){
                    Db::table($table)
                        ->where('month',$data['order_date'])
                        ->update([
                            'payment_amount' =>$data['payment_amount'],
                            'order_count' =>$data['order_count'],
                        ]);
                }
            }

        }

    }



    public function insertCheck($data){
        $table =$this->model->getTable();
        //模糊查询 按年月模糊查找
        $query = Db::table($table)
            ->where('month', 'like', $data . '%')
            ->select();
        $update = Db::table('fa_day_trade_table')
            ->where('order_date', $data)
            ->select();
        if (empty($query)) {
            //没有该月份的数据
            return $data;
        }else{
            //月份数据存在 返回查询到的数据
            return $update;
        }
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
