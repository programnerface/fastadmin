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
class Zelle extends Backend
{

    /**
     * Zelle模型对象
     * @var \app\admin\model\Zelle
     */
    protected $model = null;
    protected $dataLimit = 'personal';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Zelle;
        $this->view->assign("orderStatusList", $this->model->getOrderStatusList());
        $this->view->assign("orderCheckList", $this->model->getOrderCheckList());
        $this->assignconfig('isSuperAdmin',$this->auth->isSuperAdmin());
        $this->assignconfig('merchant_name',$this->groupcheck());
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
//            $zelle_fees =Db::name('admin')->where('id',$this->auth->id)->field('zelle_fees')->find();
//            $cash_fees =Db::name('admin')->where('id',$this->auth->id)->field('cash_fees')->find();
//            $venmo_fees =Db::name('admin')->where('id',$this->auth->id)->field('venmo_fees')->find();
//            $square_fees =Db::name('admin')->where('id',$this->auth->id)->field('square_fees')->find();
            foreach ($list as $row) {
                $row->getRelation('admin')->visible(['username']);
                $row->getRelation('admin')->visible(['zelle_fees']);
                $row->getRelation('admin')->visible(['cash_fees']);
                $row->getRelation('admin')->visible(['venmo_fees']);
                $row->getRelation('admin')->visible(['square_fees']);
				$row->getRelation('type')->visible(['name']);
				$row->getRelation('country')->visible(['name']);
				$row->getRelation('zone')->visible(['name']);
//                $row['fees'] =$zelle_fees['zelle_fees'];
//                $row['fees'] =$zelle_fees['zelle_fees'];
//                $row['amount'] =$row['price']*(1-$row['fees']);
            }
//var_dump($row['amount']);
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
        $zelle_fees =Db::name('admin')->where('id',$this->auth->id)->field('zelle_fees')->find();
        $params = $this->request->post('row/a');
        $params['fees'] =$zelle_fees['zelle_fees'];
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
//        $zelle_fees =Db::name('admin')->where('id',$this->auth->id)->field('zelle_fees')->find();
        $params = $this->request->post('row/a');
//        $params['fees'] =$zelle_fees['zelle_fees'];
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

}
