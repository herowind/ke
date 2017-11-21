<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究 )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 学校监控管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeLiveschool;
use app\guanke\validate\LiveschoolValid;
use app\guanke\service\GuankeManageSvc;

class Liveschool extends ManageController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 公司旗下的所有监控
     */
    public function index()
    {
        $params = $this->request->param();
        
        // 学校
        if (! empty($params['school_id'])) {
            $where[] = ['school_id','=',$params['school_id']];
        }
        
        // 摄像头
        if (! empty($params['camera_id'])) {
            $where[] = ['camera_id','=',$params['camera_id']];
        }
        
        $pageData = GuankeLiveschool::manage()->keywords([
            'name|school_name|camera_name',
            $this->request->param('keywords')
        ])
            ->where($where)
            ->order('sort desc')
            ->paginate(10);
        $this->assign('pageData', $pageData);
        return $this->fetch();
    }

    /**
     * 编辑监控
     */
    public function edit()
    {
        $params = $this->request->param();
        if ($this->request->isPost()) {
            $validate = new LiveschoolValid();
            $checkRst = $validate->check($params, '', 'edit');
            if ($checkRst !== true) {
                // 验证失败
                $this->error($validate->getError());
            } else {
                if($params['school_id']){
                    $params['school'] = GuankeManageSvc::getSchoolJson($params['school_id']);
                }
                if($params['camera_id']){
                    $params['camera'] = GuankeManageSvc::getCameraJson($params['camera_id']);
                }
                // 验证通过
                if ($params['id']) {
                    // 更新操作
                    $detail = GuankeLiveschool::manage()->find($params['id']);
                    $flag = $detail->save($params);
                    if ($flag !== false) {
                        $this->success('保存成功', $this->getLastUrl());
                    } else {
                        $this->error('保存失败');
                    }
                } else {
                    // 新增操作
                    $params['cid'] = $this->getCid();
                    $flag = GuankeLiveschool::create($params);
                    if ($flag !== false) {
                        $this->success('新增成功', $this->getLastUrl());
                    } else {
                        $this->error('新增失败');
                    }
                }
            }
        } else {
            if ($params['id']) {
                $detail = GuankeLiveschool::manage()->find($params['id']);
                $this->assign('detail', $detail);
            } else {
                $detail = [
                    'school_id' => $params['school_id'],
                    'isdisplay' => 1,
                ];
                $this->assign('detail', $detail);
            }
            return $this->fetch('edit');
        }
    }

    /**
     * 删除监控
     */
    public function remove()
    {
        $params = $this->request->param();
        $flag = GuankeLiveschool::manage()->delete($params['id']);
        if ($flag == 1) {
            return $this->success('删除成功');
        } else {
            return $this->error('信息不存在');
        }
    }

    /**
     * 排序变更
     */
    public function sortChange()
    {
        $id = $this->request->param('id');
        $sort = $this->request->param('sort');
        $detail = GuankeLiveschool::manage()->find($id);
        $detail->sort = $sort;
        $flag = $detail->save();
        if ($flag !== false) {
            $this->success('操作成功', '', $sort);
        } else {
            $this->error('操作失败', '', $sort);
        }
    }

    /**
     * 状态变更
     */
    public function statusChange()
    {
        $id = $this->request->param('id');
        $field = $this->request->param('field');
        $detail = GuankeLiveschool::manage()->find($id);
        
        if ($detail->$field === 1) {
            $detail->$field = 0;
        } else {
            $detail->$field = 1;
        }
        $flag = $detail->save();
        if ($flag !== false) {
            $this->success('操作成功', '', $detail->$field);
	    }else{
	        $this->error('操作失败','',$detail->$field);
	    }
	}
	
}