<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace com\utils;

/**
 * 通用函数库
 * @author oliver
 *
 */
class FncCommon{
	/**
	 * 订单生成函数
	 * @param unknown_type $prefix
	 * @return string
	 */
	public static function genericTradeCode($prefix=''){
		$utimestamp = microtime(true);
		$timestamp = floor($utimestamp);
		$milliseconds = round(($utimestamp - $timestamp) * 1000000);
		return $prefix.date('Ymdhis').$milliseconds.str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
	}
	
	public static function genericVipCard($member_id){
		return str_pad(mt_rand(1, 999), 3, '1', STR_PAD_LEFT).str_pad($member_id, 8, '0', STR_PAD_LEFT);
	}
	
	/**
	 * 距离格式化
	 * @param unknown_type $distance
	 * @param unknown_type $unit
	 * @return string
	 */
	public static function distanceFormat($distance,$unit=['m','km']){
		$distance = round($distance);
		if($distance<1000){
			return $distance.$unit[0];
		}else{
			return sprintf("%.1f%s",$distance/1000,$unit[1]);
		}
	}
	/**
	 * 获得客户端IP
	 * @return string
	 */
	public static function getClientIp(){            
	    if (getenv("HTTP_CLIENT_IP"))
	         $ip = getenv("HTTP_CLIENT_IP");
	    else if(getenv("HTTP_X_FORWARDED_FOR"))
	            $ip = getenv("HTTP_X_FORWARDED_FOR");
	    else if(getenv("REMOTE_ADDR"))
	         $ip = getenv("REMOTE_ADDR");
	    else $ip = "Unknow";
	    
	    if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))          
	        return $ip;
	    else
	        return '';
	}
	
	/**
	 * 获得客户端IP
	 * @return string
	 */
	public static function getServerIp(){
		return gethostbyname($_SERVER["SERVER_NAME"]);   
	}
	
	/**
	 * 检查手机号码格式
	 * @param $mobile 手机号码
	 */
	public static function checkMobile($mobile){
		if(preg_match('/1[34578]\d{9}$/',$mobile))
			return true;
		return false;
	}
	
	/**
	 * 检查固定电话
	 * @param $mobile
	 * @return bool
	 */
	public static function checkPhone($phone){
		if(preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/',$phone))
			return true;
		return false;
	}
	
	/**
	 * 检查邮箱地址格式
	 * @param $email 邮箱地址
	 */
	public static function checkEmail($email){
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			return true;
		return false;
	}
}