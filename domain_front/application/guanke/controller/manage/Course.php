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
// | 课程管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeCourse;
use app\guanke\validate\CourseValid;
use app\zhibo\model\ZhiboCamera; 

class Course extends ManageController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 公司旗下的所有课程
     */
    public function index()
    {
        $params = $this->request->param();
        // 课程类型
        if (in_array($params['type'], [
            '0',
            '1'
        ])) {
            $where[] = [
                'type',
                '=',
                $params['type']
            ];
        }
        // 收费类型
        if (in_array($params['feetype'], [
            '0',
            '1'
        ])) {
            $where[] = [
                'feetype',
                '=',
                $params['feetype']
            ];
        }
        
        // 学校
        if (! empty($params['school_id'])) {
            $where[] = [
                'exp',
                "FIND_IN_SET({$params['school_id']},school_ids)"
            ];
        }
        $pageData = GuankeCourse::manage()->keywords([
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
            $validate = new CourseValid();
            $checkRst = $validate->check($params, '', 'edit');
            if ($checkRst !== true) {
                // 验证失败
                $this->error($validate->getError());
            } else {
                // 验证通过
                
                //上传banner
                $rtnBanner = $this->uploadCut('banner',640,320);
                if($rtnBanner !== false){
                    $params['banner'] = $rtnBanner;
                }
                
                if ($params['id']) {
                    // 更新操作
                    $detail = GuankeCourse::manage()->find($params['id']);
                    $flag = $detail->save($params);
                    if ($flag !== false) {
                        $this->success('保存成功', $this->getLastUrl());
                    } else {
                        $this->error('保存失败');
                    }
                } else {
                    // 新增操作
                    $params['cid'] = $this->getCid();
                    $flag = GuankeCourse::create($params);
                    if ($flag !== false) {
                        $this->success('新增成功', $this->getLastUrl());
                    } else {
                        $this->error('新增失败');
                    }
                }
            }
        } else {
            if ($params['id']) {
                $detail = GuankeCourse::manage()->find($params['id']);
                $this->assign('detail', $detail);
            } else {
                $detail = [
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
        $flag = GuankeCourse::manage()->delete($params['id']);
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
        $detail = GuankeCourse::manage()->find($id);
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
        $detail = GuankeCourse::manage()->find($id);
        
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