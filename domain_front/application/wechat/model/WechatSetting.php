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
// | 公众号模型
// +----------------------------------------------------------------------
namespace app\wechat\model;

use app\manage\model\CommonMod;

class WechatSetting extends CommonMod
{
    protected $pk = 'cid';
	public function getAuthorizerInfoAttr($value,$data){
		$atthinfo = json_decode($value,true);
		if(!empty($atthinfo)){
			switch($atthinfo['service_type_info']['id']){
				case '0': 
					$atthinfo['service_type_info'] = '订阅号';
					break;
				case '1':
					$atthinfo['service_type_info'] = '订阅号';
					break;
				case '2':
					$atthinfo['service_type_info'] = '服务号';
					break;
				default:
					$atthinfo['service_type_info'] = '审核中';
					break;
			}
			switch($atthinfo['verify_type_info']['id']){
				case '-1':
					$atthinfo['verify_type_info'] = '未认证';
					break;
				case '0':
					$atthinfo['verify_type_info'] = '微信认证';
					break;
				case '1':
					$atthinfo['verify_type_info'] = '新浪微博认证';
					break;
				case '2':
					$atthinfo['verify_type_info'] = '腾讯微博认证';
					break;
				case '3':
					$atthinfo['verify_type_info'] = '未通过名称认证';
					break;
				case '4':
					$atthinfo['verify_type_info'] = '新浪微博认证';
					break;
				case '5':
					$atthinfo['verify_type_info'] = '腾讯微博认证';
					break;
				default:
					$atthinfo['verify_type_info'] = '未知';
					break;
			}
		}else{
			return [];
		}
        return $atthinfo;
    }
}