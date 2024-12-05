<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MerchantZelleorder extends Backend
{

    /**
     * MerchantZelleorder模型对象
     * @var \app\admin\model\MerchantZelleorder
     */
    protected $model = null;
    protected $dataLimit = 'personal';
//    protected $dataLimitField = 'adminid';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\MerchantZelleorder;
        $this->view->assign("orderStatusList", $this->model->getOrderStatusList());
    }
    public function import()
    {
    return parent::import();
    }
    public function export(){
        //获取当前表格
        $table = $this->model->getQuery()->getTable();
        var_dump($table);
        exit();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
