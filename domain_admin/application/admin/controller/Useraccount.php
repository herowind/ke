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
// | 客户首页
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\AdminUser;

class Useraccount extends AdminController{
	
	public function initialize(){
		parent::initialize();
	}

	/**
	 * 客户列表页
	 */
	public function index() {
		$pageData = AdminUser::admin()->paginate(20);
		$this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 添加客户
	 */
	public function add(){
		$params = $this->request->param();
		if($this->request->isPost()){
			
			if(empty($params['username'])){
				$this->error('用户名不能为空');
			}
			if(empty($params['password'])){
				$this->error('密码不能为空');
			}
			if(empty($params['realname'])){
				$this->error('联系人不能为空');
			}
			if(empty($params['mobile'])){
				$this->error('联系人手机号不能为空');
			}
			$isExist = User::where('username',$params['username'])->find();
			if($isExist){
				$this->error('用户名已经存在，请重新输入');
			}
			$params['usercode'] = uniqid('uc');
			$params['pid'] = 0;
			$params['password'] = '123456';
			
			$detailUser = User::create($params);
			if($detailUser && $detailUser->id){
				$data['cid'] = $detailUser->id;
				$data['admin_id'] = $this->getLoginId();
				$data['agent_id'] = $this->getAgentId();
				
				$data['username'] = $params['username'];
				$data['mobile'] = $params['mobile'];
				$data['realname'] = $params['realname'];
				$data['remark'] = $params['remark'];
				$data['endtime'] = date('Y-m-d',strtotime('+1year 1month'));
				AdminUser::create($data);
			}
			$this->success('用户创建成功');
		}else{
			exit($this->fetch());
		}
	}
	
	/**
	 * 编辑客户
	 */
	public function edit(){
		$params = $this->request->param();
		$this->checkValidCid($params['cid']);
		$detail =$this->adminuser;
		if($this->request->isPost()){
			if(empty($params['realname'])){
				$this->error('联系人不能为空');
			}
			if(empty($params['mobile'])){
				$this->error('联系人手机号不能为空');
			}
			$detail->mobile = $params['mobile'];
			$detail->realname = $params['realname'];
			$detail->remark = $params['remark'];
			$detail->endtime = $params['endtime'];
			$detail->save();
			$this->success('用户保存成功');
		}else{
			exit($this->fetch());
		}
	}
	
	/**
	 * 删除用户
	 */
	public function remove()
	{
		$params = $this->request->param();
		$this->checkValidCid($params['cid']);
		$detail =$this->adminuser;
		if($detail){
			$flag = $detail->delete();
			if ($flag == 1) {
				User::destroy($params['cid']);
				return $this->success('删除成功');
			}
		}
		return $this->error('信息不存在');
		
	}
}