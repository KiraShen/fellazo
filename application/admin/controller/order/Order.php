<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 订单管理
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

    protected $noNeedRight = ['start', 'pause', 'change', 'detail', 'cxselect', 'searchlist'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Order;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 生成订单号
     * 年月日（6位）+ 项目ID（2位）+ 支付人ID（3位）+ 推荐人ID（3位）+ 随机数（2位）
     */
    protected function getOrderNumber($_itemid,$_payerid,$referid){
        return date('y').date('m').date('d').substr('00' . $_itemid, -2).substr('000' . $_payerid, -3).substr('000' . $referid, -3).rand(10,99);
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $payer = model('User')->field('id,nickname')->find($params['payerid'])->getData();
            $refer = model('User')->field('id,nickname')->find($params['referid'])->getData();
            $params['payer'] = $payer['nickname'];
            $params['refer'] = $refer['nickname'];
            $item = model('Item')->field('id,item,type,price,rate,shares')->find($params['itemid'])->getData();
            $params['item'] = $item['item'];
            $params['price'] = $item['price'];
            $params['pay'] = $item['price'];
            $params['rate'] = $item['rate'];
            $params['shares'] = $item['shares'];
            $params['order'] = $this->getOrderNumber($params['itemid'],$params['payerid'],$params['referid']);
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign('itemList', build_select('row[itemid]', \app\admin\model\Item::column('id,item'), [], ['class' => 'form-control selectpicker']));
        $this->view->assign('payerList', build_select('row[payerid]', \app\admin\model\User::column('id,nickname'), [], ['class' => 'form-control selectpicker']));
        $this->view->assign('referList', build_select('row[referid]', \app\admin\model\User::column('id,nickname'), [], ['class' => 'form-control selectpicker']));
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
            $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        //$this->view->assign('itemList', build_select('row[itemid]', \app\admin\model\Item::column('id,item'), $row['itemid'], ['class' => 'form-control selectpicker forbindden']));
        //$this->view->assign('payerList', build_select('row[payerid]', \app\admin\model\User::column('id,nickname'), $row['payerid'], ['class' => 'form-control selectpicker']));
        //$this->view->assign('referList', build_select('row[referid]', \app\admin\model\User::column('id,nickname'), $row['referid'], ['class' => 'form-control selectpicker']));
        return $this->view->fetch();
    }

    /**
     * 审核
     */
    public function verify($ids = NULL){
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
            $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        //格式化$row
        $row = $row->getData();
        unset($row['id']);
        unset($row['itemid']);
        unset($row['payerid']);
        unset($row['referid']);
        unset($row['price']);
        unset($row['description']);
        unset($row['attr']);
        unset($row['status']);
        unset($row['weigh']);
        $row['createtime'] = date("Y-m-d H:i",$row['createtime']);
        $row['updatetime'] = date("Y-m-d H:i",$row['updatetime']);
        $this->view->assign("row",$row);
        return $this->view->fetch();
    }

}
