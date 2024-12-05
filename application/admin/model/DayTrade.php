<?php

namespace app\admin\model;

use think\Db;
use think\Model;


class DayTrade extends Model
{

    

    

    // 表名
    protected $name = 'day_trade_table';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
  /*  public function YearTrade()
    {
        return $this->hasOne('YearTrade', 'order_date', 'id')->joinType('INNER');
    }

    public function MonthTrade()
    {
        return $this->belongsTo('MonthTrade', 'order_date','month')->setEagerlyType(0);
    }*/
  /*public function getZelleOrder()
{
    $results = Db::table('fa_merchant_zelleorder')
        ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_zelleorder" as source')
        ->union(function($query) {
            $query->table('fa_merchant_cashorder')
                ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_cashorder" as source')
                ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
        },true)
        ->union(function($query) {
            $query->table('fa_merchant_venmoorders')
                ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_venmoorders" as source')
                ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
        },true)
        ->union(function($query) {
            $query->table('fa_merchant_squareorders')
                ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_squareorders" as source')
                ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
        },true)
        ->group('DATE(order_date)') // 确保主查询中也有 GROUP BY
        ->order('order_date', 'asc')
        ->distinct(true)
        ->select();
    $result= [];

    foreach ($results as $item) {

        $orderDate = $item['order_date'];
        $source = $item['source'];
        // 动态生成字段名
        $fieldPrefix = '';
        if ($source === 'merchant_cashorder') {
            $fieldPrefix = 'cash';
        } elseif ($source === 'merchant_zelleorder') {
            $fieldPrefix = 'zelle';
        } elseif ($source === 'merchant_squareorders') {
            $fieldPrefix = 'square';
        } elseif ($source === 'merchant_venmoorders') {
            $fieldPrefix = 'venmo';
        }


        if ($fieldPrefix) {
            // 初始化日期组
            if (!isset($result[$orderDate])) {
                $result[$orderDate] = [
                    'order_date' => $orderDate,
                    'day_count' => 0,   // 日订单数
                    'day_amount' => 0.0 // 日交易额
                ];
            }

            // 添加字段值
            $result[$orderDate][$fieldPrefix . '_ids'] = $item['ids'];
            $result[$orderDate][$fieldPrefix . '_count'] = $item['count'];
            $result[$orderDate][$fieldPrefix . '_amount'] = $item['total_amount'];
            // 累加日订单数和日交易额
            $result[$orderDate]['day_count'] += $item['count'];
            $result[$orderDate]['day_amount'] += (float)$item['total_amount'];
        }

    }
    $this->checkDate();
    // 获取fa_day_trade_table表中的所有订单日期
    $allDatesInTable = Db::table('fa_day_trade_table')
        ->field('DATE(order_date) as order_date')
        ->select();
// 获取表中的日期数组
      $allDatesInTable = array_map(function($item) {
          return $item['order_date']; // 提取出order_date
      }, $allDatesInTable);
// 获取$result数组中的所有订单日期
      $resultDate = array_keys($result);

// 找出两个数组中不相同的日期（在表中存在但在$result中不存在的日期）
      $datesToDelete = array_diff($allDatesInTable, $resultDate);

// 如果有需要删除的日期
      if (!empty($datesToDelete)) {
          foreach ($datesToDelete as $date) {
              // 删除fa_day_trade_table表中对应日期的数据
              Db::table('fa_day_trade_table')
                  ->where('order_date', $date)
                  ->delete();
          }
      }
      //需要插入数据
      $datesToInsert = array_diff($resultDate, $allDatesInTable);
    if (!empty($datesToInsert)) {
        //判断日期是否在表中存在，如果存在则进行更新，如果不存在则进行插入
        foreach ($datesToInsert as $date) {
            $date =$result[$date];
           $query =db::table('fa_day_trade_table')
                ->where('order_date', $date['order_date'])->field('id')->select();
           if (empty($query)) {
               //表中没有该日期进行 插入
               var_dump("insert");
                db::table('fa_day_trade_table')->insert([
                    'order_date' => $date['order_date'],
                    'payment_amount' => $date['day_amount'],
                    'order_count' => $date['day_count'],
                    'order_zelle' =>$date['zelle_amount'],
                    'order_cash' =>$date['cash_amount'],
                    'order_venmo' =>$date['venmo_amount'],
                    'order_square' =>$date['square_amount'],
                ]);
           }else{
               //表中有该日期进行更新
               var_dump("update");
               db::table('fa_day_trade_table')
                   ->where('order_date', $date['order_date'])
                   ->update([
                       'payment_amount' => $date['day_amount'],
                       'order_count' => $date['day_count'],
                       'order_zelle' =>$date['zelle_amount'],
                       'order_cash' =>$date['cash_amount'],
                       'order_venmo' =>$date['venmo_amount'],
                       'order_square' =>$date['square_amount'],
                   ]);
           }
        }
    }

}
 public function checkDate(){
     // 查找重复的日期数据并删除
     $duplicates = Db::table('fa_day_trade_table')
         ->field('DATE(order_date) as order_date, GROUP_CONCAT(id ORDER BY id DESC) as ids, COUNT(*) as total')
         ->group('DATE(order_date)')
         ->having('total > 1')
         ->select();
     foreach ($duplicates as $duplicate) {
         $ids = explode(',', $duplicate['ids']); // 按逗号分割为数组
         array_shift($ids);
         if (!empty($ids)) {
             Db::table('fa_day_trade_table')->delete($ids); // 删除其余旧数据
         }
     }
 }*/




}
