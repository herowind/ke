<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究  )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 讲师管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeTeacher;
use app\guanke\validate\TeacherValid;
use app\manage\model\UserMember;

class Teacher extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}

	/**
	 * 公司旗下的所有老师
	 */
	public function index() {
	    $params = $this->request->param(); 
	    
	    //学校
	    if(!empty($params['school_id'])){
	        $where[] = ['exp',"FIND_IN_SET({$params['school_id']},school_ids)"];
	    }
		$pageData = GuankeTeacher::manage()->keywords(['name|mobile',$this->request->param('keywords')])->where($where)->order('sort desc')->paginate(10);
		$this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 从会员表中查询老师
	 */
	public function search(){
	    $mobile = $this->request->param('mobile');
	    if(empty($mobile)){
	        exit($this->fetch());
	    }else{
	        $list = UserMember::field('id,nickname,mobile,avatar')->manage()->where('mobile',$mobile)->limit(20)->select();
	        $this->success('查询成功','',$list);
	    }
	}
	
	/**
	 * 创建老师
	 */
	public function create(){
	    $member_id = $this->request->param('member_id');
	    //查询会员信息
	    $member = UserMember::manage()->find($member_id);
	    if($member){
	        //查询老师信息
	        $teacher = GuankeTeacher::manage()->where('member_id',$member_id)->find();
	        if($teacher){
	            $this->error('该老师已经存在');
	        }
	        $data = [
	            'cid' => $this->getCid(),
	            'member_id' => $member_id,
	            'avatar' =>$member->avatar,
	            'name' =>$member->nickname,
	            'mobile' =>$member->mobile,
	        ];
	        $teacher = GuankeTeacher::create($data);
	        if($teacher){
	            $this->success('新增成功');
	        }else{
	            $this->error('添加失败');
	        }
	    }else{
	        $this->error('会员信息不存在');
	    }
	}
	
	/**
	 * 编辑老师
	 */
	public function edit() {
	    $params = $this->request->param();
	    if($this->request->isPost()){
	        $validate = new TeacherValid();
	        $checkRst = $validate->check($params,'','edit');
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            
	            //上传头像
	            $rtnLogo = $this->uploadCut('avatar');
	            if($rtnLogo !== false){
	                $params['avatar'] = $rtnLogo;
	            }
	            if($params['id']){
	                //更新操作
	                $detail = GuankeTeacher::manage()->find($params['id']);
	                $flag = $detail->save($params);
	                if($flag !== false){
	                    $this->success('保存成功',$this->getLastUrl());
	                }else{
	                    $this->error('保存失败');
	                }
	            }else{
	                //新增操作
	                $params['cid'] = $this->getCid();
	                $flag = GuankeTeacher::create($params);
	                if($flag !== false){
	                    $this->success('新增成功',$this->getLastUrl());
	                }else{
	                    $this->error('新增失败');
	                }
	            }
	        }
	    }else{
	        if($params['id']){
	            $detail = GuankeTeacher::manage()->find($params['id']);
	            $this->assign('detail',$detail);
	        }else{
	            $detail = [
	                'isdisplay' => 1,
	            ];
	            $this->assign('detail',$detail);
	        }
	         
	        return $this->fetch('edit');
	    }
	}
	
	/**
	 * 删除老师
	 */
	public function remove() {
	    $params = $this->request->param();
	    $flag = GuankeTeacher::manage()->delete($params['id'],true);
	    if($flag==1){
	        return $this->success('删除成功');
	    }else{
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
	    $detail = GuankeTeacher::manage()->find($id);
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
	public function statusChange(){
	    $id = $this->request->param('id');
	    $field = $this->request->param('field');
	    $detail = GuankeTeacher::manage()->find($id);
	    
	    if($detail->$field === 1){
	        $detail->$field = 0;
	    }else{
	        $detail->$field = 1;
	    }
	    $flag = $detail->save();
	    if($flag !== false){
	        $this->success('操作成功','',$detail->$field);
	    }else{
	        $this->error('操作失败','',$detail->$field);
	    }
	}
	
}