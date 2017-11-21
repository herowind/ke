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
// | 直播摄像头管理
// +----------------------------------------------------------------------
namespace app\zhibo\controller\manage;

use app\manage\controller\ManageController;
use app\zhibo\model\ZhiboCamera;
use app\zhibo\validate\CameraValid;

class Camera extends ManageController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 公司旗下的所有摄像头
     */
    public function index()
    {
        $params = $this->request->param();
        $pageData = ZhiboCamera::manage()->keywords([
            'name|code',
            $this->request->param('keywords')
        ])
            ->order('sort desc')
            ->paginate(10);
        $this->assign('pageData', $pageData);
        return $this->fetch();
    }

    /**
     * 编辑摄像头
     */
    public function edit()
    {
        $params = $this->request->param();
        if ($this->request->isPost()) {
            $validate = new CameraValid();
            $checkRst = $validate->check($params, '', 'edit');
            if ($checkRst !== true) {
                // 验证失败
                $this->error($validate->getError());
            } else {
                // 验证通过
                if ($params['id']) {
                    // 更新操作
                    $detail = ZhiboCamera::manage()->find($params['id']);
                    $flag = $detail->save($params);
                    if ($flag !== false) {
                        $this->success('保存成功', $this->getLastUrl());
                    } else {
                        $this->error('保存失败');
                    }
                } else {
                    // 新增操作
                    $params['cid'] = $this->getCid();
                    $flag = ZhiboCamera::create($params);
                    if ($flag !== false) {
                        $this->success('新增成功', $this->getLastUrl());
                    } else {
                        $this->error('新增失败');
                    }
                }
            }
        } else {
            if ($params['id']) {
                $detail = ZhiboCamera::manage()->find($params['id']);
                $this->assign('detail', $detail);
            } else {
                $detail = [
                    'type' => 1
                ];
                $this->assign('detail', $detail);
            }
            
            return $this->fetch('edit');
        }
    }
    
    /**
     * 预览直播
     */
    public function preview(){
        $id = $this->request->param('id');
        $title = $this->request->param('title');
        $camera = ZhiboCamera::manage()->find($id);
        $camera->title = $title;
        $this->assign('camera',$camera);
        exit($this->fetch());
    }

    /**
     * 删除摄像头
     */
    public function remove()
    {
        $params = $this->request->param();
        $flag = ZhiboCamera::manage()->delete($params['id']);
        if ($flag == 1) {
            return $this->success('删除成功');
        } else {
            return $this->error('信息不存在');
        }
    }
    
    /**
     * 名称变更
     */
    public function name(){
        $id = $this->request->param('id');
        $detail = ZhiboCamera::manage()->find($id);
        if($this->request->isPost()){
            $detail->name = $this->request->param('name');
            $flag = $detail->save();
            if($flag !== false){
                return $this->success('修改成功');
            }else{
                return $this->error('修改失败');
            }
        }else{
            $this->assign('detail',$detail);
            exit($this->fetch());
            
        }
    }

    /**
     * 排序变更
     */
    public function sortChange()
    {
        $id = $this->request->param('id');
        $sort = $this->request->param('sort');
        $detail = ZhiboCamera::manage()->find($id);
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
        $detail = ZhiboCamera::manage()->find($id);
        
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