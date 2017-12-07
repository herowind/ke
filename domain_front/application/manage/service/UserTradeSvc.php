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
// | 管理者登录控制器
// +----------------------------------------------------------------------
namespace app\manage\service;

use think\facade\App;
use app\manage\model\UserTrade;
use app\manage\model\User;

class UserTradeSvc
{
	/**
	 * 支付方式：点卡代扣
	 * 类型：支付
	 * @param unknown $cid
	 * @param unknown $mid
	 * @param unknown $goods
	 * @return number[]|string[]
	 */
    public static function memberPayDaikou($cid, $mid, $goods)
    {
    	//验证是否付费
    	$detail = UserTrade::where('member_id',$mid)->where('validdate',date('Y-m-d'))->find();
    	if(empty($detail)){
    		$price = config('manage.zhiboprice.live');
    		//执行扣费
    		$data = [
    				'cid' => $cid,
    				'member_id' => $mid,
    				'goodstype' => $goods['type'],
    				'goodsinfo' => json_encode($goods,JSON_UNESCAPED_UNICODE),
    				'type'		=> 1,
    				'price'		=> $price,
    				'validdate'	=> date('Y-m-d'),
    				'paytype'	=> 1,
    				'ispay'		=> 1,
    		];
    		$detail = UserTrade::create($data);
    		if($detail){
    			//操作成功，主账户扣费
    			$flag = User::where('id',$this->getCid())->update(['amount' => ['exp',"amount-{$price}"]]);
    			//暂时不对扣款失败作后续操作（非必要验证）
    		}
    	} 
    	return ['code'=>1,'msg'=>'操作成功'];
    }
}
