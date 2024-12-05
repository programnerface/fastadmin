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
class Year extends Backend
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
        $this->getYearTrade();
    }


    public function getYearTrade(){
        $orderData = controller('DayTrade');
        $result=$orderData->getOderData();
        $yearlyData = $this->YearSum($result);

        foreach ($yearlyData as $data){

            $insert=$this->insertCheck($data['latest_date']);

            foreach ($insert as $item){
                //$insert第一条数据里面只有2个字段 说明需要执行插入
                if (count(array_keys($insert[0])) == 2){
                    if ($item['order_type'] == 'Zelle'){
                        Db::table('fa_year_trade_table')->insert([
                            'year' => $data['latest_date'],
                            'order_type' => 'Zelle',
                            'payment_amount' =>$data['zelle_amount'],
                            'order_count'=>$data['zelle_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Cash'){
                        Db::table('fa_year_trade_table')->insert([
                            'year' => $data['latest_date'],
                            'order_type' => 'Cash',
                            'payment_amount' =>$data['cash_amount'],
                            'order_count'=>$data['cash_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Venmo'){
                        Db::table('fa_year_trade_table')->insert([
                            'year' => $data['latest_date'],
                            'order_type' => 'Venmo',
                            'payment_amount' =>$data['venmo_amount'],
                            'order_count'=>$data['venmo_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Square'){
                        Db::table('fa_year_trade_table')->insert([
                            'year' => $data['latest_date'],
                            'order_type' => 'Square',
                            'payment_amount' =>$data['square_amount'],
                            'order_count'=>$data['square_count'],
                        ]);
                    };
                }else{
                   if ($item['order_type'] == 'Zelle'){
                        Db::table('fa_year_trade_table')
                            ->where('year',$data['latest_date'])
                            ->where('order_type','Zelle')
                            ->update([
                            'payment_amount' =>$data['zelle_amount'],
                            'order_count'=>$data['zelle_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Cash'){
                        Db::table('fa_year_trade_table')
                            ->where('year',$data['latest_date'])
                            ->where('order_type','Cash')
                            ->update([
                            'payment_amount' =>$data['cash_amount'],
                            'order_count'=>$data['cash_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Venmo'){
                        Db::table('fa_year_trade_table')
                            ->where('year',$data['latest_date'])
                            ->where('order_type','Venmo')
                            ->update([
                            'payment_amount' =>$data['venmo_amount'],
                            'order_count'=>$data['venmo_count'],
                        ]);
                    }elseif ($item['order_type'] == 'Square'){
                        Db::table('fa_year_trade_table')
                            ->where('year',$data['latest_date'])
                            ->where('order_type','Square')
                            ->update([
                            'payment_amount' =>$data['square_amount'],
                            'order_count'=>$data['square_count'],
                        ]);
                    };
                }
            }

        }

    }
    public function insertCheck($data){

        $table =$this->model->getTable();
        $results = Db::table($table)
            ->where('year', 'like', $data . '%')
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
//按年份分组并统计每年数据总和
    function YearSum($result) {
        // 初始化一个用于存储每年数据的数组
        $yearlyData = [];

        // 遍历 $result 数组
        foreach ($result as $orderDate => $data) {
            // 提取年份（从订单日期中获取年份）
            $year = substr($orderDate, 0, 4);  // 提取年份（例如 '2024'）

            // 如果该年份没有记录，初始化年份数据
            if (!isset($yearlyData[$year])) {
                $yearlyData[$year] = [
                    'year' => $year,
                    'latest_date' => $orderDate,  // 初始化为当前日期
                    'total_day_count' => 0,  // 年总订单数
                    'total_day_amount' => 0.00,  // 年总交易额
                    'zelle_count' => 0,  // 年总zelle交易数
                    'zelle_amount' => 0.0,  // 年总zelle交易额
                    'cash_count' => 0,  // 年总现金交易数
                    'cash_amount' => 0.0,  // 年总现金交易额
                    'venmo_count' => 0,  // 年总venmo交易数
                    'venmo_amount' => 0.0,  // 年总venmo交易额
                    'square_count' => 0,  // 年总square交易数
                    'square_amount' => 0.0,  // 年总square交易额
                ];
            }

            // 更新最新的日期
            if (strtotime($orderDate) > strtotime($yearlyData[$year]['latest_date'])) {
                $yearlyData[$year]['latest_date'] = $orderDate;
            }

            // 获取每个字段的值，并设置默认值
            $dayCount = isset($data['day_count']) ? $data['day_count'] : 0;
            $dayAmount = isset($data['day_amount']) ? $data['day_amount'] : 0.0;
            $zelleCount = isset($data['zelle_count']) ? $data['zelle_count'] : 0;
            $zelleAmount = isset($data['zelle_amount']) ? $data['zelle_amount'] : 0.0;
            $cashCount = isset($data['cash_count']) ? $data['cash_count'] : 0;
            $cashAmount = isset($data['cash_amount']) ?$data['cash_amount'] : 0.0;
            $venmoCount = isset($data['venmo_count']) ? $data['venmo_count'] : 0;
            $venmoAmount = isset($data['venmo_amount']) ? $data['venmo_amount'] : 0.0;
            $squareCount = isset($data['square_count']) ? $data['square_count'] : 0;
            $squareAmount = isset($data['square_amount']) ? $data['square_amount'] : 0.0;

            // 累加该日期的各个字段到对应年份的总数中
            $yearlyData[$year]['total_day_count'] += $dayCount;
            $yearlyData[$year]['total_day_amount'] += $dayAmount;
            $yearlyData[$year]['zelle_count'] += $zelleCount;
            $yearlyData[$year]['zelle_amount'] += $zelleAmount;
            $yearlyData[$year]['cash_count'] += $cashCount;
            $yearlyData[$year]['cash_amount'] += $cashAmount;
            $yearlyData[$year]['venmo_count'] += $venmoCount;
            $yearlyData[$year]['venmo_amount'] += $venmoAmount;
            $yearlyData[$year]['square_count'] += $squareCount;
            $yearlyData[$year]['square_amount'] += $squareAmount;
        }

        // 将每年的交易总额等字段进行四舍五入，保留两位小数
        foreach ($yearlyData as &$yearData) {
            $yearData['total_day_amount'] = round($yearData['total_day_amount'], 2);
            $yearData['zelle_amount'] = round($yearData['zelle_amount'], 2);
            $yearData['cash_amount'] = round($yearData['cash_amount'], 2);
            $yearData['venmo_amount'] = round($yearData['venmo_amount'], 2);
            $yearData['square_amount'] = round($yearData['square_amount'], 2);
        }

        // 最终返回按年分组并计算了总和的结果
        return $yearlyData;
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
