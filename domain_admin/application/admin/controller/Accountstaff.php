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
// | 子账户管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\service\AdminLoginSvc;
use app\admin\model\AdminUser;

class Accountstaff extends AdminController
{

    public function initialize()
    {
        parent::initialize();
    }
    
    public function index(){
    	switch (AdminLoginSvc::getRole()){
    		case 'supperadmin':
    			$where[] = ['pid','=',0];
    			$where[] = ['id','>',1];
    			break;
    		case 'agent':
    			$where[] = ['pid','=',$this->getLoginId()];
    			break;
    		default:
    			$this->error('您无权操作','/admin/useraccount/index');
    	}
    	$pageData = Admin::where($where)->paginate(20);
    	$this->assign('pageData',$pageData);
    	return $this->fetch();
    }

	/**
	 * 添加子账号
	 */
	public function add(){
		$params = $this->request->param();
		if($this->request->isPost()){
			if(empty($params['username'])){
				$this->error('手机号不能为空');
			}
			if(empty($params['realname'])){
				$this->error('员工姓名不能为空');
			}
			$isExist = Admin::where('username',$params['username'])->find();
			if($isExist){
				$this->error('手机号已被占用，请重新输入');
			}
			switch (AdminLoginSvc::getRole()){
				case 'supperadmin':
					$params['pid'] = 0;
					$params['type'] = 2;
					break;
				case 'agent':
					$params['pid'] = $this->getLoginId();
					$params['type'] = 3;
					break;
				default:
					$this->error('您无权操作','/admin/useraccount/index');
			}
			$params['mobile'] = $params['username'];
			$params['password'] = '123456';
			$admin = Admin::create($params);
			if($admin && $admin->id){
				$this->success('账号创建成功');
			}else{
				$this->success('账号创建失败，如遇疑问请联系管理员');
			}
		}else{
			exit($this->fetch());
		}
	}
	/**
	 * 重置密码
	 */
	public function resetpass()
	{
		$params = $this->request->param();
		switch (AdminLoginSvc::getRole()){
			case 'supperadmin':
				$where[] = ['pid','=',0];
				break;
			case 'agent':
				$where[] = ['pid','=',$this->getLoginId()];
				break;
			default:
				$this->error('您无权操作','/admin/useraccount/index');
		}
		$where[] = ['id','=',$params['id']];
		$detail = Admin::where($where)->find();
		$detail->password = '123456';
		$detail->save();
		$this->success('密码重置成功，默认新密码： 123456');
	}
	
	/**
	 * 删除员工
	 */
	public function remove()
	{
		$params = $this->request->param();
		switch (AdminLoginSvc::getRole()){
			case 'supperadmin':
				$where[] = ['pid','=',0];
				break;
			case 'agent':
				$where[] = ['pid','=',$this->getLoginId()];
				break;
			default:
				$this->error('您无权操作','/admin/useraccount/index');
		}
		$where[] = ['id','=',$params['id']];
		$detail = Admin::where($where)->find();
		if($detail){
			//判断该员工下面是否有客户
			$isExist = AdminUser::where('admin_id',$params['id'])->find();
			if($isExist){
				$this->error('该员工下面存在用户资源，请转移后删除');
			}
			//开始删除，真实删除
			$flag = $detail->delete(true);
			if ($flag == 1) {
				return $this->success('删除成功');
			}
		}
		return $this->error('员工不存在');
	
	}
}