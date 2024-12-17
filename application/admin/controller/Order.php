<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\Order
     */
    protected $model = null;
    protected $dataLimit = 'auth';
    protected $noNeedRight =['invoice'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Order;
        $this->view->assign("walletsList", $this->model->getWalletsList());
        $this->view->assign("orderStateList", $this->model->getOrderStateList());
        $this->assignconfig('isSuperAdmin',$this->auth->isSuperAdmin());//管理员
        $this->assignconfig('operateedit', $this->operate());
        $this->view->assign('style', $this->merstyle());
        $this->view->assign('edit', $this->meredit());

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


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
                    ->with(['admin','country','zone'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('admin')->visible(['username']);
				$row->getRelation('country')->visible(['name']);
				$row->getRelation('zone')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    public function add(){
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');


        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $fee_rate =Db::name('order')->where('id',$this->auth->id)->field('fee_rate')->find();
        $params = $this->preExcludeFields($params);

        if(empty($params['fee_rate'])){
            $params['fee_rate'] =$fee_rate['fee_rate']??0.08;
        }else{
            $params['fee_rate'] =$params['fee_rate'];
        }
        $params['fee'] = $params['amount_total']*$params['fee_rate'];
        $params['amount_due'] =$params['amount_total']*(1-$params['fee_rate']);

        $params['uint_price'] =$params['amount_total']/$params['quantity'];//单价
        $params['recipient_name'] =$params['customer'];


        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }
    public function edit($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $fee_rate =Db::name('order')->where('id',$this->auth->id)->field('fee_rate')->find();

        $params = $this->request->post('row/a');
        if(empty($params['fee_rate'])){
            $params['fee_rate'] =$fee_rate['fee_rate']??0.08;
        }else{
            $params['fee_rate'] =$params['fee_rate'];
        }

        $params['fee'] = $params['amount_total']*$params['fee_rate'];
        $params['amount_due'] =$params['amount_total']*(1-$params['fee_rate']);

        $params['uint_price'] =$params['amount_total']/$params['quantity'];//单价

        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }
    public function merchantedit($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $fee_rate =Db::name('order')->where('id',$this->auth->id)->field('fee_rate')->find();
        $params = $this->request->post('row/a');
        if(empty($params['fee_rate'])){
            $params['fee_rate'] =$fee_rate['fee_rate']??0.08;
        }else{
            $params['fee_rate'] =$params['fee_rate'];
        }

        $params['fee'] = $params['amount_total']*$params['fee_rate'];
        $params['amount_due'] =$params['amount_total']*(1-$params['fee_rate']);

        $params['uint_price'] =$params['amount_total']/$params['quantity'];//单价
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            $this->imglog();
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    public function merstyle(){
        if ($this->auth->id == 1){
            return "none";
        }else{
            return "inline-block";
        }
    }
    public function meredit(){
        if ($this->auth->id == 1){
            return "edit";
        }else{
            return "merchantedit";
        }
    }
    public function operate()
    {
        if($this->auth->id == 1){
            return 'order/edit';
        }else{
            return 'order/merchantedit';
        }
    }

    public function invoice($order_num)
    {
        $table = $this->model->getTable();
        $query = Db::table($table)->where('id',$order_num)->select();

        $data =[];
        if (empty($query)) {
            return "订单号不存在！";
        }else {
            foreach ($query as $data) {
            }
        }

        //发票链接写入数据库
        if (empty($data['invoice'])) {
            $invoce = request()->domain()."/sFGucSHOMQ.php/order/invoice.html?order_num=".$data['id'];
            Db::table($table)->where('id',$data['id'])->update([
                'invoice' => $invoce,
            ]);
        }else{
            $data['invoice'] = $data['invoice'];
        }


        if ($data['country_id']){
            $country = Db::table('fa_country')->field('name')->where('country_id',$data['country_id'])->value('name');
        }
        if ($data['zone_id']){
            $zone_name = Db::table('fa_zone')->field('code')->where('zone_id',$data['zone_id'])->value('code');
        }
//        if($data['product_type_ids']){
//            $productname = Db::table('fa_product_type')->field('name')->where('id',$data['product_type_ids'])->value('name');
//        }
        $email=Db::table('fa_admin')->where('id',$data['admin_id'])->value('email');


//        if ($data['account']){
//            $accountname = Db::table('fa_mer_ven')->field('name')->where('account',$data['account'])->value('name');
//        }

        if(empty($zone_name)){
//            return json([
//                'status' => 'error',
//                'message' => '州/省不能为空，请重新填写', // 错误信息
//            ]);
            throw new \Exception('州/省不能为空，请重新填写');
        }

        $data =[
            'order_date'=>$data['date'],
            'order_num' => $data['id'],
            'confirm_num' => $data['confirm_num'],//确认码
            'customer' =>$data['customer'],//顾客
            'street_adress'=>$data['street_adress'],//街道
            'city'=>$data['city'].$zone_name.$data['zip'],//城市 州code 邮编
//            'Payment_address' =>$data['payment_address'],
//            'Payment_address2' =>$data['payment_address2'],
            'country_name' => $country,
//            'city' =>$data['city'].' '.$zone_name.' '.$data['postcode'],
            'recipient_name' =>$data['recipient_name'],//收件人
            'productname'=>$data['product_name'],//产品名称
            'quantity' =>$data['quantity'],//数量
            'uint_price' =>$data['uint_price'],//单价
            'amount_total' =>$data['amount_total'],//金额
            'shipping_cost'=>$data['shipping_cost'],//运费
            'total' =>$data['amount_total']+$data['shipping_cost'],//金额+运费
            'fee' =>$data['fee'],
            'amount_due' =>$data['amount_due'],//应结算
            'order_state' =>$data['order_state'],//订单状态
            'tracking_num' =>$data['tracking_num'],//运单号
            'email' =>$email,//邮箱

        ];


        // 通过 assign 方法传递数据
        $this->view->assign('data', $data);

        return $this->view->fetch('order/invoice');
    }
    public function imglog()
    {
        //获取用户订单中的截图路径
        $query=Db::name('order')->where('admin_id',$this->auth->id)->field('payment_img')->value('payment_img');
        $username =Db::name('admin')->where('id',$this->auth->id)->field('username')->value('username');
        file_put_contents('D:/website/phpstudy_pro/WWW/fastadmin.com/runtime/log/' .'订单截图'. '.txt', '用户名'."$username\n".'图片路径'."$query\n", FILE_APPEND);
    }
}
