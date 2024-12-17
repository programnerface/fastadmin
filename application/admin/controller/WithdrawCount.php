<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class WithdrawCount extends Backend
{

    /**
     * WithdrawCount模型对象
     * @var \app\admin\model\WithdrawCount
     */
    protected $model = null;
    protected $dataLimit = 'personal';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\WithdrawCount;
        $this->assignconfig('isSuperAdmin',$this->auth->isSuperAdmin());
//        $this->GetWithdrawCount();
        $this->count();
//        var_dump($this->count());
//        exit();
//        var_dump($this->GetWithdrawCount());
//        exit;
    }


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['admin'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('admin')->visible(['username']);
                $admin_id=$row['admin']['id'];
                $groupName=$this->auth->getGroups($admin_id);
                $groupName = array_column($groupName, 'name');

                $row['groups_text']=$groupName[0] ?? null;;
            }
//var_dump( $row);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    public function GetWithdrawCount()
    {
        $admin_id = $this->auth->id;
        $groupName=$this->auth->getGroups($admin_id);
        $groupName = array_column($groupName, 'name');
        $group_text=$groupName[0] ?? null;
        if($group_text  == '供应商'){
            $this->GetVenWithdrawCount();
        }else{
            $this->GetMerWithdrawCount();
        }
    }
    public function GetMerWithdrawCount(){
        $admin_id =$this->auth->id;
        //获取zelle订单总金额
        $zelleprice = $this->getAmountById('fa_zelle','price');
        //zelle订单应结算总额
        $zelleamount = $this->getcount('fa_zelle','amount');
        $cashprice =$this->getcount('fa_cash','price');
        $cashamount = $this->getcount('fa_cash','amount');
        $venmoprice = $this->getcount('fa_venmo','price');
        $venmoamount = $this->getcount('fa_venmo','amount');
        $squareprice = $this->getcount('fa_square','price');
        $squareamount = $this->getcount('fa_square','amount');
        //总金额
        $total_price =$zelleprice+$cashprice+$venmoprice+$squareprice;
        //应结算
        $total_amount=$zelleamount+$cashamount+$venmoamount+$squareamount;
        //已提现
        $withdrawal = Db::table('fa_withdrawn_log')->where(['admin_id'=>$admin_id,'withdrawal_check'=>'审核通过'])->sum('withdrawal_usamount');
        //已退款
        $refund = Db::table('fa_refund')->where(['admin_id'=>$admin_id,'refund_status'=>'退款成功'])->sum('refund_amount');
        //未结算
        $unset=$total_amount-$withdrawal-$refund;



       $table =$this->model->getTable();

        $query = Db::table($table)->where('admin_id',$this->auth->id)->count();

        if ($query > 0){
            //用户已存在 更新数据
            Db::table($table)->where('admin_id',$admin_id)->update([
                'total_amount' => $total_price, //总交易额
                'set_amount'   => $total_amount, //应结算
                'withdrawn_amount' => $withdrawal, //已提现
                'refund_amount' => $refund, //已退款
                'unset_amount' => $unset,//未结算
            ]);
        }else{
            //插入
            Db::table($table)->insert([
                'admin_id' => $this->auth->id,
                'total_amount' => $total_price, //总交易额
                'set_amount'   => $total_amount, //应结算
                'withdrawn_amount' => $withdrawal, //已提现
                'refund_amount' => $refund, //已退款
                'unset_amount' => $unset,//未结算
            ]);
        }


    }
    public function count(){
        //
        $admin_id =$this->auth->id;
        $zelleamount = $this->getAmountById('Zelle','amount_total'); //金额
        $zellecount = $this->getCountById('Zelle');//订单数
        $zelle_fees =$this->getAmountById('Zelle','fee'); //手续费
        $zelleamountdue = $this->getAmountById('Zelle','amount_due');//应结算

        $cashamount = $this->getAmountById('Cash App','amount_total'); //金额
        $cashcount = $this->getCountById('Cash App');//订单数
        $cash_fees =$this->getAmountById('Cash App','fee'); //手续费
        $cashamountdue = $this->getAmountById('Cash App','amount_due');//应结算

        $squareamount = $this->getAmountById('Square','amount_total'); //金额
        $squarecount = $this->getCountById('Square');//订单数
        $square_fees =$this->getAmountById('Square','fee'); //手续费
        $squareamountdue = $this->getAmountById('Square','amount_due');//应结算

        $venmoamount = $this->getAmountById('Venmo','amount_total'); //金额
        $venmocount = $this->getCountById('Venmo');//订单数
        $venmo_fees =$this->getAmountById('Venmo','fee'); //手续费
        $venmoamountdue = $this->getAmountById('Venmo','amount_due');//应结算

        //手续费


        //总金额
        $amount_total=$zelleamount+$cashamount+$squareamount+$venmoamount;
        //总手续费
        $amount_fees=$zelle_fees+$cash_fees+$venmo_fees+$square_fees;
        //总应结算
        $amount_due=$zelleamountdue+$cashamountdue+$squareamountdue+$venmoamountdue;
        //已提现
        $withdrawal = Db::table('fa_withdrawn_log')->where(['admin_id'=>$admin_id,'withdrawal_status'=>'已提现'])->sum('withdrawal_usamount');
        //已退款
        $refund = Db::table('fa_refund')->where(['admin_id'=>$admin_id,'refund_status'=>'已退款'])->sum('refund_amount');
        //未结算
        $unset=$amount_due-$withdrawal-$refund;

        //管理员数据
        $adminamount =Db::name('order')->whereIn('order_state',['Paid','Delivered'])->sum('amount_total');
        $adminfees =Db::name('order')->whereIn('order_state',['Paid','Delivered'])->sum('fee');
        $adminamountdue =Db::name('order')->whereIn('order_state',['Paid','Delivered'])->sum('amount_due');
        $adminwithdrawal = Db::table('fa_withdrawn_log')->where(['withdrawal_status'=>'已提现'])->sum('withdrawal_usamount');
        $adminrefund = Db::table('fa_refund')->where(['refund_status'=>'已退款'])->sum('refund_amount');
        //未结算
        $adminunset=$adminamountdue-$adminwithdrawal-$adminrefund;



        //插入缺失的订单类型数据
        $this->insertcheck();
        $this->updateDayTable('Zelle',$zelleamount,$zellecount,$zelleamountdue,$zelle_fees);
        $this->updateDayTable('Cash App',$cashamount,$cashcount,$cashamountdue,$cash_fees);
        $this->updateDayTable('Venmo',$venmoamount,$venmocount,$venmoamountdue,$venmo_fees);
        $this->updateDayTable('Square',$squareamount,$squarecount,$squareamountdue,$square_fees);
        $table =$this->model->getTable();

        $query = Db::table($table)->where('admin_id',$this->auth->id)->count();

        if ($query > 0){
            //用户已存在 更新数据
            if ($this->auth->id == 1){
                Db::table($table)->where('admin_id',$this->auth->id)->update([
                    'total_amount' => $adminamount, //总交易额
                    'fees' => $adminfees,//手续费
                    'set_amount'   => $adminamountdue, //应结算
                    'withdrawn_amount' => $adminwithdrawal, //已提现
                    'refund_amount' => $adminrefund, //已退款
                    'unset_amount' => $adminunset,//未结算
                ]);
            }else{
                Db::table($table)->where('admin_id',$admin_id)->update([
                    'total_amount' => $amount_total, //总交易额
                    'fees'=>$amount_fees,//手续费
                    'set_amount'   => $amount_due, //应结算
                    'withdrawn_amount' => $withdrawal, //已提现
                    'refund_amount' => $refund, //已退款
                    'unset_amount' => $unset,//未结算
                ]);
            }

        }else{
            //插入
            Db::table($table)->insert([
                'admin_id' => $this->auth->id,
                'total_amount' => $amount_total, //总交易额
                'fees' => $amount_fees,//手续费
                'set_amount'   => $amount_due, //应结算
                'withdrawn_amount' => $withdrawal, //已提现
                'refund_amount' => $refund, //已退款
                'unset_amount' => $unset,//未结算
            ]);
        }


    }

     public function insertcheck()
     {
         $query = Db::name('day_trade_table')
             ->field('wallets, COUNT(*) as count') // 按钱包类型统计
             ->where('admin_id', $this->auth->id)
             ->whereIn('wallets', ['Zelle', 'Square', 'Cash App', 'Venmo'])
             ->group('wallets') // 按 wallets 分组
             ->having('COUNT(*) > 0') // 确保每种类型有数据
             ->select();

        // 检查是否每个 wallets 类型都有数据
         $walletsRequired = ['Zelle', 'Square', 'Cash App', 'Venmo'];
         $walletsResult = array_column($query, 'wallets');
         $missingWallets = array_diff($walletsRequired, $walletsResult);
         foreach ($missingWallets as $wallet) {
             DB::name('day_trade_table')->insert([
                 'wallets' =>$wallet,
                 'admin_id'=>$this->auth->id,
             ]);
         }

            return $query;
//         if (!empty($missingWallets)) {
//             // 某些类型没有数据，可以手动处理或抛出异常
//             throw new \Exception('以下钱包类型缺少数据: ' . implode(', ', $missingWallets));
//         }

     }
     public function updateDayTable($type,$amount,$count,$amount_due,$fees)
     {
         $query= Db::name('day_trade_table')
             ->where(['admin_id'=>$this->auth->id,'wallets'=>$type])
             ->update([
                 'payment_amount'=>$amount,
                 'order_count'=>$count,
                 'payment_due' =>$amount_due,
                 'fees'=>$fees,
             ]);
     }
    public function getAmountById($type,$field){
        $query = Db::name('order')->where(['admin_id'=>$this->auth->id,'wallets'=>$type])
            ->whereIn('order_state',['Paid','Delivered'])->sum($field);
        return $query;
    }
    public function getCountById($type){
        $query = Db::name('order')->where(['admin_id'=>$this->auth->id,'wallets'=>$type])
            ->whereIn('order_state',['Paid','Delivered'])->count();
        return $query;
    }
    public function getcount($table,$field){

        $query = DB::table($table)->where(['admin_id'=>$this->auth->id,'order_check'=>'审核通过'])->sum($field);

        return $query;
    }
    public function getcountven($table,$field){
        $query = Db::table($table)
            ->alias('z') // 设置别名
            ->join('fa_mer_ven mv', 'z.account = mv.account AND z.name = mv.name') // 关联条件
            ->field('z.account, z.name') // 选择需要的字段
            ->where(['mv.type' => 'Zelle', 'mv.ven_id' => $this->auth->id]) // 其他条件
            ->select();

        $accounts = [];
        foreach ($query as $data) {
            $accounts = $data['account'];
            $name = $data['name'];
        }

        $accounts = array_column($query, 'account');

        $query = DB::table($table)
            ->whereIn('account', $accounts ?? [])
            ->where('name', $name ?? '')
            ->where('order_check', '审核通过')->sum($field);


        return $query;
    }
    public function GetVenWithdrawCount()
    {
        // 查询 fa_mer_ven 中的 account 和 type
//        $query = Db::table('fa_mer_ven')->field('account, type')->where('ven_id', $this->auth->id)->select();


//        // 按类型分组获取对应的 accounts
//        $accountsByType = [];
//        foreach ($query as $item) {
//            $accountsByType[$item['type']][] = $item['account'];
//        }

        $admin_id =$this->auth->id;
        //获取zelle订单总金额
        $zelleprice = $this->getcountven('fa_zelle','price');

        //zelle订单应结算总额
        $zelleamount = $this->getcountven('fa_zelle','amount');
        $cashprice =$this->getcountven('fa_cash','price');
        $cashamount = $this->getcountven('fa_cash','amount');
        $venmoprice = $this->getcountven('fa_venmo','price');
        $venmoamount = $this->getcountven('fa_venmo','amount');
        $squareprice = $this->getcountven('fa_square','price');
        $squareamount = $this->getcountven('fa_square','amount');
        //总金额
        $total_price =$zelleprice+$cashprice+$venmoprice+$squareprice;
        //应结算
        $total_amount=$zelleamount+$cashamount+$venmoamount+$squareamount;
        //已提现
        $withdrawal = Db::table('fa_withdrawn_log')->where(['admin_id'=>$admin_id,'withdrawal_check'=>'审核通过'])->sum('withdrawal_usamount');
        //已退款
        $refund = Db::table('fa_refund')->where(['admin_id'=>$admin_id,'refund_status'=>'退款成功'])->sum('refund_amount');
        //未结算
        $unset=$total_amount-$withdrawal-$refund;


        $table =$this->model->getTable();

        $query = Db::table($table)->where('admin_id',$this->auth->id)->count();

        if ($query > 0){
            //用户已存在 更新数据
            Db::table($table)->where('admin_id',$admin_id)->update([
                'total_amount' => $total_price, //总交易额
                'set_amount'   => $total_amount, //应结算
                'withdrawn_amount' => $withdrawal, //已提现
                'refund_amount' => $refund, //已退款
                'unset_amount' => $unset,//未结算
            ]);
        }else{
            //插入
            Db::table($table)->insert([
                'admin_id' => $this->auth->id,
                'total_amount' => $total_price, //总交易额
                'set_amount'   => $total_amount, //应结算
                'withdrawn_amount' => $withdrawal, //已提现
                'refund_amount' => $refund, //已退款
                'unset_amount' => $unset,//未结算
            ]);
        }

    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
