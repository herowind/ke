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
// | 公众号模板模型
// +----------------------------------------------------------------------
namespace app\wechat\model;

use app\manage\model\CommonMod;

class WechatUsertemplatetask extends CommonMod
{
	public function getTotypetextAttr($value,$data){
		$arr = config('data.template.tasktype');
		return $arr[$data['totype']];
	}
	
	public function getIssendtextAttr($value,$data){
		$arr = config('data.template.tasksendflag');
		return $arr[$data['issend']];
	}
	
	public function getIspublishtextAttr($value,$data){
		$arr = config('data.template.taskpublishflag');
		return $arr[$data['ispublish']];
	}
	
	public function getFormAttr($value){
		if($value){
			return json_decode($value,value);
		}
		return [];
	}
}