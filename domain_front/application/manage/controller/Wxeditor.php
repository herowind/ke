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
// | 管理首页
// +----------------------------------------------------------------------
namespace app\manage\controller;

class Wxeditor extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}

	public function loadtemp()
	{
		$type = $this->request->param('type');
		if(!in_array($type,array('title','text','img','tpl','scene','follow')))
		{
			echo '错误的模板类型 T_T';
			exit;
		}
		return $this->fetch('temp_'.$type);
	}
	
}