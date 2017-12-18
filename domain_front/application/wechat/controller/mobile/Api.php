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
// | 公众号api
// +----------------------------------------------------------------------
namespace app\wechat\controller\mobile;

use EasyWeChat\Factory;
use app\wechat\model\WechatSetting;
use app\mobile\service\MobileLoginSvc;


class Api {
	
	/**
	 * 获得二维码链接地址
	 */
	public function qrcode_url(){
		$cid = cookie('currentCid');
		if($cid){
			return WechatSetting::where('cid',$cid)->value('qrcode_url');
		}
		return '';
	}
	
	/**
	 * 获得历史页面链接地址
	 */
	public function history_url(){
		$cid = cookie('currentCid');
		if($cid){
			return WechatSetting::where('cid',$cid)->value('history_url');
		}
		return '';
	}
	
	/**
	 * 是否关注公众号
	 * -1：未设置公众号
	 * 1：关注了
	 * 0：未关注
	 */
	public function issubscribe(){
		$cid = cookie('currentCid');
		$member = MobileLoginSvc::getSession($cid);
		if(!empty($member) && $member->openid){
			$openPlatform = Factory::openPlatform(config('wechat.component'));
			$wechat = WechatSetting::field('appid,authorizer_refresh_token')->find($cid);
			if($wechat){
				$officialAccount = $openPlatform->officialAccount($wechat->appid, $wechat->authorizer_refresh_token);
				$user = $officialAccount->user->get($member->openid);
				return $user['subscribe'];
			}
		}
		return -1;
		
	}
	
	public function jssdkconfig(){
		$cid = cookie('currentCid');
		$openPlatform = Factory::openPlatform(config('wechat.component'));
		$wechat = WechatSetting::field('appid,authorizer_refresh_token')->find($cid);
		if($wechat){
			$officialAccount = $openPlatform->officialAccount($wechat->appid, $wechat->authorizer_refresh_token);
			$config = $officialAccount->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
			return $config;
		}
		return '';
	}
}