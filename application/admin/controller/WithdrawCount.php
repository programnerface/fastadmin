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
        $this->GetWithdrawCount();
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
    public function GetWithdrawCount(){
        $admin_id =$this->auth->id;
        //获取zelle订单总金额
        $zelleprice = $this->getcount('fa_zelle','price');
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
    public function getcount($table,$field){


        $query = DB::table($table)->where(['admin_id'=>$this->auth->id,'order_check'=>'审核通过'])->sum($field);

        return $query;
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
