<?php

namespace app\admin\controller;

use app\admin\controller\auth\Admin;
use app\common\controller\Backend;
use tests\thinkphp\library\think\cacheTest;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class YearTrade extends Backend
{

    /**
     * YearTrade模型对象
     * @var \app\admin\model\YearTrade
     */
    protected $model = null;
    protected $dataLimit = 'personal';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\YearTrade;
        $this->GetYearTrade();
    }
    public function GetYearTrade(){
        $admin_id =$this->GetAdminId();
        $table=$this->model->getTable();
        $monthTrade = controller('MonthTrade');
        $monthTrade->deleteDuplicates($table,'year','%Y',$admin_id);
        if ($admin_id ==1){

        }else{
            $this->GetYearcountById();
        }
    }

    public function GetYearcountById()
    {
        $query = Db::table('fa_day_trade_table')
            ->field([
                'MAX(order_date) as latest_date',                 // 每月的最新日期
                'DATE_FORMAT(order_date, "%Y") as year',      // 按 年-月 格式分组
                'SUM(payment_amount) as total_amount',            // 计算交易总额
                'SUM(order_count) as total_orders',               // 计算总订单数
                'SUM(zelle_count) as zelle_counts',               //zelle总订单数
                'SUM(order_zelle) as zelle_amount',               //zelle交易总额
                'SUM(cash_count) as cash_counts',                 //cash_count总订单数
                'SUM(order_cash) as cash_amount',
                'SUM(venmo_count) as venmo_counts',                //cash_count总订单数
                'SUM(order_venmo) as venmo_amount',
                'SUM(square_count) as square_counts',               //square_count总订单数
                'SUM(order_square) as square_amount',
            ])
            ->group('DATE_FORMAT(order_date, "%Y")')          // 按月份分组
            ->where('admin_id',$this->GetAdminId())
            ->select();

        foreach ($query as $data) {
            $insertbyid=$this->insertCheckByid($data['latest_date']);
            foreach ($insertbyid as $item){
                if (count(array_keys($insertbyid[0])) == 2){
                    if ($item['order_type'] == 'Zelle'){
                        Db::table('fa_year_trade_table')->insert([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Zelle',
                            'payment_amount' =>$data['zelle_amount'],
                            'order_count'=>$data['zelle_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Cash'){
                        Db::table('fa_year_trade_table')->insert([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Cash',
                            'payment_amount' =>$data['cash_amount'],
                            'order_count'=>$data['cash_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Venmo'){
                        Db::table('fa_year_trade_table')->insert([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Venmo',
                            'payment_amount' =>$data['venmo_amount'],
                            'order_count'=>$data['venmo_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Square'){
                        Db::table('fa_year_trade_table')->insert([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Square',
                            'payment_amount' =>$data['square_amount'],
                            'order_count'=>$data['square_counts'],
                        ]);
                    }
                }else{
                    if ($item['order_type'] == 'Zelle'){
                        Db::table('fa_year_trade_table')->where([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Zelle',
                        ])->update([
                            'payment_amount' =>$data['zelle_amount'],
                            'order_count'=>$data['zelle_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Cash'){
                        Db::table('fa_year_trade_table')->where([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Cash',
                        ])->update([
                            'payment_amount' =>$data['cash_amount'],
                            'order_count'=>$data['cash_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Venmo'){
                        Db::table('fa_year_trade_table')->where([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Venmo',
                        ])->update([
                            'payment_amount' =>$data['venmo_amount'],
                            'order_count'=>$data['venmo_counts'],
                        ]);
                    }elseif ($item['order_type'] == 'Square'){
                        Db::table('fa_year_trade_table')->where([
                            'admin_id'=>$this->GetAdminId(),
                            'year' => $data['latest_date'],
                            'order_type' => 'Square',
                        ])->update([
                            'payment_amount' =>$data['square_amount'],
                            'order_count'=>$data['square_counts'],
                        ]);
                    }
                }
            }
        }
    }
    public function GetAdminId()
    {
        $admin = new Admin();
        $adminid=$this->auth->id;

        return $adminid;
    }

    public function insertCheckByid($data){

        $table =$this->model->getTable();

        $results = Db::table($table)
            ->where([
                'year' => ['like', $data . '%'],
                'admin_id' => $this->GetAdminId()
            ])
            ->whereIn('order_type', ['Zelle', 'Cash', 'Venmo', 'Square'])//订单类型
            ->select();

        // 定义所有必需的订单类型
        $requiredOrderTypes = ['Zelle', 'Cash', 'Venmo', 'Square'];

        // 提取年份
        $year = null;
        foreach ($results as $item) {
            $year = substr($item['year'], 0, 4); // 获取年份部分
            break; // 只需提取一次
        }

        // 提取当前的订单类型
        $existingOrderTypes = array_column($results, 'order_type');

        // 找出缺失的类型
        $missingOrderTypes = array_diff($requiredOrderTypes, $existingOrderTypes);

        // 如果没有缺失类型，直接返回原始结果
        if (empty($missingOrderTypes)) {
            return $results;
        }

        // 构造缺失的类型和年份的返回结果
        $missingResults = [];
        foreach ($missingOrderTypes as $missingType) {
            $missingResults[] = [
                'order_type' => $missingType,
                'year' => $year,
            ];
        }

        return $missingResults;
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
