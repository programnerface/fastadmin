<?php

namespace app\admin\controller;

use app\admin\controller\auth\Admin;
use app\common\controller\Backend;
use  think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MonthTrade extends Backend
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
        $this->GetMonthTrade();


    }


    public function GetAdminId()
    {
        $admin = new Admin();
        $adminid=$this->auth->id;

        return $adminid;
    }

    public function GetMonthTrade(){
        //删除重复数据
        $this->CheckData();
        $table=$this->model->getTable();
        $admin_id=$this->GetAdminId();
        $this->deleteDuplicates($table,'month','%Y-%m',$admin_id);
        if($admin_id == 1){
            $this->GetOrderCount();
        }else{
            $this->GetOrderCountById();
        }

    }

    public function GetOrderCount(){

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
            $table=$this->model->getTable();

            if ($insert == $data['latest_date']){
                Db::table($table)->insert([
                    'month'=>$data['latest_date'],
                    'payment_amount' =>$data['total_amount'],
                    'order_count' =>$data['total_orders'],
                ]);
            }else{
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

    //
    public function CheckData(){
        $table=$this->model->getTable();
        // 获取日统计表的日期和 ID
        $dayquery = Db::table('fa_day_trade_table')->field('order_date')->select();

        // 将日统计表的日期提取成数组
        $dayDates = array_column($dayquery, 'order_date');

        // 获取月统计表的日期和 ID
        $monthquery = Db::table($table)->field('id, month as order_date')->select();

        // 将月统计表的日期和 ID 进行比对
        foreach ($monthquery as $row) {
            if (!in_array($row['order_date'], $dayDates)) {
                // 删除月统计表中与日统计表日期不同的记录
                Db::table($table)->where('id', $row['id'])->delete();
            }
        }
    }

    public function GetOrderCountById(){

        $query = Db::table('fa_day_trade_table')
            ->field([
                'MAX(order_date) as latest_date',                 // 每月的最新日期
                'DATE_FORMAT(order_date, "%Y-%m") as month',      // 按 年-月 格式分组
                'SUM(payment_amount) as total_amount',            // 计算交易总额
                'SUM(order_count) as total_orders',               // 计算总订单数
            ])
            ->group('DATE_FORMAT(order_date, "%Y-%m")')          // 按月份分组
            ->where('admin_id',$this->GetAdminId())
            ->select();

        foreach ($query as $data){
            $insertbyid = $this->insertCheckById($data['latest_date']);
            $table=$this->model->getTable();
            if ($insertbyid == $data['latest_date']){
                Db::table($table)->insert([
                    'admin_id'=>$this->GetAdminId(),
                    'month'=>$data['latest_date'],
                    'payment_amount' =>$data['total_amount'],
                    'order_count' =>$data['total_orders'],
                ]);
            }else{
                foreach ($insertbyid as $insertdata){
                   Db::table($table)->where([
                       'admin_id'=>$this->GetAdminId(),
                       'month'=>$insertdata['order_date'],
                   ])
                   ->update([
                       'payment_amount' =>$data['total_amount'],
                       'order_count' =>$data['total_orders'],
                   ]);
                }
            }
        }
    }

    public function insertCheckById($data)
    {
        $table =$this->model->getTable();
        //模糊查询 按年月模糊查找
        $query = Db::table($table)
            ->where([
                'month' => ['like', $data . '%'],
                'admin_id' => $this->GetAdminId()
            ])
            ->select();

        $update = Db::table('fa_day_trade_table')
            ->where([
                'order_date' => ['like', $data . '%'],
                'admin_id' => $this->GetAdminId()
            ])
            ->select();

        if (empty($query)) {
            //没有该月份的数据
            return $data;
        }else{
            //月份数据存在 返回查询到的数据
            return $update;
        }
    }

    public function checkMonthData(){
        $table=$this->model->getTable();
        // 查找重复的月份数据并删除
        $duplicates = Db::table($table)
            ->field('DATE_FORMAT(month, "%Y-%m") as month_group, GROUP_CONCAT(id ORDER BY month DESC) as ids, COUNT(*) as total')
            ->group('month_group') // 按年份和月份分组
            ->having('total > 1') // 找出重复的月份
            ->select();

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate['ids']); // 按逗号分割为数组
            array_shift($ids); // 移除数组中的第一个元素（即最新的记录 ID）
            if (!empty($ids)) {
                Db::table($table)->delete($ids); // 删除其余旧数据
            }
        }
    }

    /**
     * 删除重复数据
     *
     * @param string $table 数据表名
     * @param string $field 要检查重复的字段（日期、月份或年份字段）
     * @param string $groupFormat 分组格式（如 "%Y-%m-%d"、"%Y-%m"、"%Y"）
     * @param int|null $admin_id 可选的 admin_id 条件
     */
    function deleteDuplicates($table, $field, $groupFormat, $admin_id = null)
    {
        $query = Db::table($table)
            ->field("DATE_FORMAT($field, '$groupFormat') as group_key, GROUP_CONCAT(id ORDER BY id DESC) as ids, COUNT(*) as total")
            ->group('group_key,admin_id')
            ->having('total > 1');
//        var_dump(111111);
        // 如果有 admin_id 参数，添加查询条件
        if ($admin_id !== null) {
            $query->where('admin_id', $admin_id);
        }

        // 获取重复数据
        $duplicates = $query->select();

        // 如果没有重复数据，直接返回
        if (empty($duplicates)) {
            return;
        }

        // 删除重复数据，保留每组最新的一条
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate['ids']); // 按逗号分割为数组

            array_shift($ids); // 移除数组中的第一个元素（即最新记录的 ID）

            if (!empty($ids)) {
                Db::table($table)->delete($ids); // 删除其余旧数据
            }
        }
        /* //看用户id是否存在
       $querybyid = Db::table($table)->where('admin_id', $admin_id)->count();
     // 如果有 admin_id 参数，添加查询条件
       if ($admin_id !== null) {
           $query->where('admin_id', $admin_id);
           if ($querybyid > 2) {
               // 获取重复数据
               $duplicates = $query->select();

               // 删除重复数据，保留每组最新的一条
               foreach ($duplicates as $duplicate) {
                   $ids = explode(',', $duplicate['ids']); // 按逗号分割为数组
                   array_shift($ids); // 移除数组中的第一个元素（即最新记录的 ID）
                   if (!empty($ids)) {
                       Db::table($table)->where('admin_id', $duplicate['admin_id'])->delete($ids); // 删除其余旧数据
                   }
               }
           }
       }
       else{
           // 获取重复数据
           $duplicates = $query->select();

           // 删除重复数据，保留每组最新的一条
           foreach ($duplicates as $duplicate) {
               $ids = explode(',', $duplicate['ids']); // 按逗号分割为数组
               array_shift($ids); // 移除数组中的第一个元素（即最新记录的 ID）
               if (!empty($ids)) {
                   Db::table($table)->delete($ids); // 删除其余旧数据
               }
           }
       }*/

    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
