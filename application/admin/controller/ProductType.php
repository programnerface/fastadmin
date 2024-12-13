<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class ProductType extends Backend
{

    /**
     * ProductType模型对象
     * @var \app\admin\model\ProductType
     */
    protected $model = null;
    protected $dataLimit = 'auth';
    protected $noNeedRight = ["index"];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\ProductType;

    }

    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->with(['admin'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        foreach ($list as $row) {
            $row->getRelation('admin')->visible(['username']);
        }
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
