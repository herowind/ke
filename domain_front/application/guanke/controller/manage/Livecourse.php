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
// | 直播课程管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeLivecourse;
use app\guanke\validate\LivecourseValid;
use app\guanke\service\GuankeManageSvc;
use app\zhibo\model\ZhiboCamera;

class Livecourse extends ManageController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 所有直播课程
     */
    public function index()
    {
        $params = $this->request->param();
        // 收费类型
        if (in_array($params['feetype'], ['0','1'])) {
            $where[] = ['feetype','=',$params['feetype']];
        }
        
        // 学校
        if (! empty($params['course_id'])) {
            $where[] = ['course_id','=',$params['course_id']];
        }
        
        // 老师
        if (! empty($params['teacher_id'])) {
            $where[] = ['teacher_id','=',$params['teacher_id']];
        }
        
        // 摄像头
        if (! empty($params['camera_id'])) {
            $where[] = ['camera_id','=',$params['camera_id']];
        }
        
        $pageData = GuankeLivecourse::manage()->keywords([
            'name',
            $this->request->param('keywords')
        ])
            ->where($where)
            ->order('sort desc')
            ->paginate(10);
        $this->assign('pageData', $pageData);
        return $this->fetch();
    }
    
    /**
     * 编辑课程
     */
    public function edit()
    {
        $params = $this->request->param();
        if ($this->request->isPost()) {
            $validate = new LivecourseValid();
            $checkRst = $validate->check($params, '', 'edit');
            if ($checkRst !== true) {
                // 验证失败
                $this->error($validate->getError());
            } else {
                // 验证通过
                
                if($params['course_id']){
                    $params['course'] = GuankeManageSvc::getCourseJson($params['course_id']);
                }
                if($params['teacher_id']){
                    $params['teacher'] = GuankeManageSvc::getTeacherJson($params['teacher_id']);
                }
                if($params['camera_id']){
                    $params['camera'] = GuankeManageSvc::getCameraJson($params['camera_id']);
                }
                
                if ($params['id']) {
                    // 更新操作
                    $detail = GuankeLivecourse::manage()->find($params['id']);
                    $flag = $detail->save($params);
                    if ($flag !== false) {
                        $this->success('保存成功', $this->getLastUrl());
                    } else {
                        $this->error('保存失败');
                    }
                } else {
                    // 新增操作
                    $params['cid'] = $this->getCid();
                    $flag = GuankeLivecourse::create($params);
                    if ($flag !== false) {
                        $this->success('新增成功', $this->getLastUrl());
                    } else {
                        $this->error('新增失败');
                    }
                }
            }
        } else {
            if ($params['id']) {
                $detail = GuankeLivecourse::manage()->find($params['id']);
                $this->assign('detail', $detail);
            } else {
                $detail = [
                    'course_id' => $params['course_id'],
                    'isdisplay' => 1
                ];
                $this->assign('detail', $detail);
            }
            
            return $this->fetch('edit');
        }
    }

    /**
     * 删除课程
     */
    public function remove()
    {
        $params = $this->request->param();
        $flag = GuankeLivecourse::manage()->delete($params['id']);
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
        $detail = GuankeLivecourse::manage()->find($id);
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
        $detail = GuankeLivecourse::manage()->find($id);
        
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