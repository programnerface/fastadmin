<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Day extends Backend
{

    /**
     * DayTrade模型对象
     * @var \app\admin\model\DayTrade
     */
    protected $model = null;
    protected $searchFields = [];
    protected $relationSearch = true;
    protected $dataLimit = 'personal';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\DayTrade;
        $this->getZelleOrder();

    }

    /**
     * 查看
     */
 /*   public function index()
    {
        $this->relationSearch = true;
        $this->searchFields ="day_trade_table.order_date,month_trade_table.month";
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(["monthtrade"])
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with(["monthtrade"])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return parent::index();
    }*/



    //获取日交易额
    public function GetDayTrade(){

        //zeelle
        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        //获取同一天的所有订单之和
        $zelleorder = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
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
    }

    public function getZelleOrder()
    {
        /*$results = Db::table('fa_merchant_zelleorder')
            ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_zelleorder" as source')
            ->union(function ($query) {
                $query->table('fa_merchant_cashorder')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_cashorder" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->union(function ($query) {
                $query->table('fa_merchant_venmoorders')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_venmoorders" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->union(function ($query) {
                $query->table('fa_merchant_squareorders')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_squareorders" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->group('DATE(order_date)') // 确保主查询中也有 GROUP BY
            ->order('order_date', 'asc')
            ->distinct(true)
            ->select();
        $result = [];

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

        }*/
        $result = $this->getOderData();

        $this->checkDate();
        // 获取fa_day_trade_table表中的所有订单日期
        $allDatesInTable = Db::table('fa_day_trade_table')
            ->field('DATE(order_date) as order_date')
            ->select();

        // 获取$result数组中的所有订单日期
        $resultDate = array_keys($result);

        // 获取表中的日期数组
        $allDatesInTable = array_map(function ($item) {
            return $item['order_date']; // 提取出order_date
        }, $allDatesInTable);


        //在$result有的日期，但是day_trade表没有
        $datesToInsert = array_diff($resultDate, $allDatesInTable);
        $datesToDelete = array_diff($allDatesInTable, $resultDate);
        //根据$datesToInsert获取$result数组里的数据
        $datesToUpdate = array_intersect($resultDate, $allDatesInTable);
        // 如果有需要删除的日期
        if (!empty($datesToDelete)) {
            foreach ($datesToDelete as $date) {
                // 删除fa_day_trade_table表中对应日期的数据
                Db::table('fa_day_trade_table')
                    ->where('order_date', $date)
                    ->delete();
            }
        }
        if (!empty($datesToInsert)) {
            //判断日期是否在表中存在，如果存在则进行更新，如果不存在则进行插入
            foreach ($datesToInsert as $date) {
                $date = $result[$date];
                $query = db::table('fa_day_trade_table')
                    ->where('order_date', $date['order_date'])->field('id')->select();
                if (empty($query)) {
                    //表中没有该日期进行 插入
//                    var_dump("insert");
                    db::table('fa_day_trade_table')->insert([
                        'order_date' => $date['order_date'],
                        'payment_amount' => $date['day_amount']??0.0,
                        'order_count' => $date['day_count']??0,
                        'order_zelle' => $date['zelle_amount']??0.0,
                        'order_cash' => $date['cash_amount']??0.0,
                        'order_venmo' => $date['venmo_amount']??0.0,
                        'order_square' => $date['square_amount']??0.0,
                    ]);
                }
            }
        }else{
            foreach ($datesToUpdate as $date) {
                $date = $result[$date];
               db::table('fa_day_trade_table')
                    ->where('order_date', $date['order_date'])
                    ->update([
                          'payment_amount' => $date['day_amount']??0.0,
                          'order_count' => $date['day_count']??0,
                          'order_zelle' => $date['zelle_amount']??0.0,
                          'order_cash' => $date['cash_amount']??0.0,
                          'order_venmo' => $date['venmo_amount']??0.0,
                          'order_square' => $date['square_amount']??0.0,
                    ]);
            }
        }


    }

    public function getOderData()
    {
        $results = Db::table('fa_merchant_zelleorder')
            ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_zelleorder" as source')
            ->union(function ($query) {
                $query->table('fa_merchant_cashorder')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_cashorder" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->union(function ($query) {
                $query->table('fa_merchant_venmoorders')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_venmoorders" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->union(function ($query) {
                $query->table('fa_merchant_squareorders')
                    ->field('DATE(order_date) as order_date, GROUP_CONCAT(id) as ids, COUNT(*) as count, SUM(amount) as total_amount, "merchant_squareorders" as source')
                    ->group('DATE(order_date)'); // 必须在子查询中也加 GROUP BY
            }, true)
            ->group('DATE(order_date)') // 确保主查询中也有 GROUP BY
            ->order('order_date', 'asc')
            ->distinct(true)
            ->select();
        $result = [];

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
        return $result;
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
