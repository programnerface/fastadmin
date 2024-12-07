<?php

namespace app\admin\controller;

use app\common\controller\Backend;

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
        $this->assignconfig('merchant_name',$this->auth->check('zelle/merchant_name'));
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

}
