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
// | 用户交易模型
// +----------------------------------------------------------------------

namespace app\manage\model;

class UserTrade extends CommonMod{
	//转换成数组
	public function getGoodsinfoAttr($value){
		if($value){
			return json_decode($value,true);
		}
		return [];
	}
	
	public function getGoodstypetextAttr($value,$data){
		$types = config('manage.zhiboprice.type');
		return $types[$data['goodstype']];
	}
}