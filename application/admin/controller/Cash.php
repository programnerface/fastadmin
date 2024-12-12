<?php

namespace app\admin\controller;

use app\admin\library\Auth;
use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Cash extends Backend
{

    /**
     * Cash模型对象
     * @var \app\admin\model\Cash
     */
    protected $model = null;
    protected $dataLimit = 'auth';
//    protected $dataLimit = 'personal';
    protected $noNeedRight =['invoice'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Cash;
        $this->view->assign("orderStatusList", $this->model->getOrderStatusList());
        $this->view->assign("orderCheckList", $this->model->getOrderCheckList());
        $this->assignconfig('isSuperAdmin',$this->auth->isSuperAdmin());
        $this->assignconfig('merchant_name',$this->groupcheck());
        $this->view->assign('style', $this->merstyle());
        $this->view->assign('adminstyle', $this->adminstyle());
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
                    ->with(['admin','type','country','zone'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('admin')->visible(['username']);
                $row->getRelation('admin')->visible(['zelle_fees']);
                $row->getRelation('admin')->visible(['cash_fees']);
                $row->getRelation('admin')->visible(['venmo_fees']);
                $row->getRelation('admin')->visible(['square_fees']);
                $row->getRelation('type')->visible(['name']);
                $row->getRelation('country')->visible(['name']);
                $row->getRelation('zone')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    public function groupcheck(){
        //

        $admin_id = $this->auth->id;
        $groupName=$this->auth->getGroups($admin_id);
        $groupName = array_column($groupName, 'name');
        $groupName =$groupName[0];
        if ($groupName == '商户'){
            return false;
        }else{
            return true;
        }

    }
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');
        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

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
    public function import()
    {

        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, 'w');
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding !== 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);

        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $v['COLUMN_COMMENT'] = explode(':', $v['COLUMN_COMMENT'])[0]; //字段备注有:时截取
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表

            $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {
                    $insert[] = $row;
                }
            }
//            var_dump($insert);
//            exit;
            foreach ($insert as $key => $data) {
                //更新id
                $data['ID']=Db::table($table)->order('id', 'desc')->value('id');
                $product_type_ids = Db::table('fa_product_type')->field('id')->where('name',$data['product_type_ids'])->find();
//               var_dump($product_type_ids);
////               exit;
                $country_id = Db::table('fa_country')->field('country_id')->where('name',$data['country_id'])->find();
                $zone_id = Db::table('fa_zone')->field('zone_id')->where('name',$data['zone_id'])->find();
//               $admin_id = Db::table('fa_admin')->field('user_id')->where('name',$data['admin_id'])->find();
                $data['product_type_ids'] =$product_type_ids['id']?? null;

                $data['country_id'] =$country_id['country_id']?? null;
                $data['zone_id'] =$zone_id['zone_id']?? null;
                $data['fees'] =floatval(str_replace('%', '', $data['fees'])) / 100;
                $data['price']=str_replace('$','',$data['price'])?? null;
                $data['amount']=str_replace('$','',$data['amount'])?? null;
                $insert[$key] = $data;
            }
//            var_dump($insert);
//            exit();
//var_dump($insert);
//            exit();

        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {

                    if (empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }


            $this->model->saveAll($insert);
        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success();
    }
    public function venderadd()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');

        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);
        $params['u_price'] =$params['price']/$params['quantity'];//单价
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.vendoradd' : $name) : $this->modelValidate;
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
    public function merchantadd()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');
        $params['order_date']=date('Y-m-d');

        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);//应结算
        $params['u_price'] =$params['price']/$params['quantity'];//单价
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.vendoradd' : $name) : $this->modelValidate;
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
    public function venderedit($ids = null){
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
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');
        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);
        $params['u_price'] =$params['price']/$params['quantity'];//单价

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
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');
        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);
        $params['u_price'] =$params['price']/$params['quantity'];//单价
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
        $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
        $params = $this->request->post('row/a');
        if(empty($params['fees'])){
            $params['fees'] =$cash_fees['cash_fees'];
        }else{
            $params['fees'] =$params['fees'];
        }
        $params['amount'] =$params['price']*(1-$params['fees']);

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

    public function merstyle()
    {
        $admin_id = $this->auth->id;
        $groupName=$this->auth->getGroups($admin_id);
        $groupName = array_column($groupName, 'name');
        $groupName =$groupName[0];
        if ($groupName == '商户'){
            return "inline-block";
        }else{
            return "none";
        }
    }
    public function adminstyle()
    {
        if ($this->auth->id == 1){
            return "none";
        }else{
            return "inline-block";
        }
    }
    public function invoice($order_num)
    {
        $table = $this->model->getTable();
        $query = Db::table($table)->where('order_num',$order_num)->select();

        $data =[];
        if (empty($query)) {
            return "订单号不存在！";
        }else {
            foreach ($query as $data) {
            }
        }

        //发票链接写入数据库
        if (empty($data['vendor_invoice'])) {
            $invoce = request()->domain()."/sFGucSHOMQ.php/cash/invoice.html?order_num=".$data['order_num'];
            Db::table($table)->where('id',$data['ID'])->update([
                'vendor_invoice' => $invoce,
            ]);
        }else{
            $data['vendor_invoice'] = $data['vendor_invoice'];
        }

        if ($data['country_id']){
            $country = Db::table('fa_country')->field('name')->where('country_id',$data['country_id'])->value('name');
        }
        if ($data['zone_id']){
            $zone_name = Db::table('fa_zone')->field('code')->where('zone_id',$data['zone_id'])->value('code');
        }
        if($data['product_type_ids']){
            $productname = Db::table('fa_product_type')->field('name')->where('id',$data['product_type_ids'])->value('name');
        }
        $email=Db::table('fa_admin')->where('id',$data['admin_id'])->value('email');
        if($data['order_status'] == '已发货'){
            $data['order_status'] = "Shipped";
        }else if($data['order_status'] == '已入账'){
            $data['order_status'] = "Paid";
        }else{
            $data['order_status'] = "Unpaid";
        }


        $data =[
            'order_date'=>$data['order_date'],
            'order_num' => $data['order_num'],
            'payer' =>'Name: '.$data['payer'],
            'Payment_address' =>'Address: '.$data['payment_address'],
            'Payment_address2' =>'Address: '.$data['payment_address2'],
            'country_name' =>'Country: '. $country,
            'city' =>$data['city'].' '.$zone_name.' '.$data['postcode'],
            'shipping_name' =>'Name: '.$data['shipping_name'],
            'productname'=>$productname,
            'quantity' =>$data['quantity'],
            'u_price' =>$data['u_price'],
            'price' =>$data['price'],
            'fees' =>($data['price']-$data['amount']),
            'amount' =>$data['amount'],
            'waybill_num' =>$data['waybill_num'],
            'email' =>$email,
            'order_status' =>$data['order_status'],
            'account' =>$data['account'],
        ];
        // 通过 assign 方法传递数据
        $this->view->assign('data', $data);

        return $this->view->fetch('cash/invoice');
    }

}

