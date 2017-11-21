<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\common\service;
use com\utils\FncCommon;

use com\utils\FncCrypt;

use think\Db;

/***
 * 描述：日志业务逻辑
* @since		2017-4-17
* @version		$Id$
*/
class LogSvc{
	
	public static function adminLog($info){
		$user = LoginSvc::getLoginUser();
		$data = [
			'admin_id'			=> $user['admin_id'],
			'content'			=> $info,
			'log_ip'			=> FncCommon::getClientIp(),
			'log_url'			=> request()->baseUrl(),
			'update_time'		=> time(),
			'create_time'		=> time(),
		];
	    Db::table('app_admin_log')->save($data);
	}
}
