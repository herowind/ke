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
// | 微信设置
// +----------------------------------------------------------------------
return [ 
		'component' => [ 
				'log' => [ 
						'file' => '../logs/easywechat_'.date('Ymd').'.log',
						'level' => 'debug' 
				],
				'oauth' => [
						'scopes'   => ['snsapi_userinfo'],
						'callback' => APP_SITE.'/mobile/passport/loginwx.html',
						
				],
				'app_id' => 'wx1114642a77425468',
				'secret' => 'd0c002791502e950f0c0b8cc882cab17',
				'token' => 'guanke',
				'aes_key' => 'KXLT0BdLBTsWx637REQLUFRTOmMqPrastrGqimyMc8n' 
				
		],
		
		'oauth_userinfo' => [
				'scopes'   => ['snsapi_base'],
				'callback' => APP_SITE.'/mobile/passport/loginwx.html',
		],
		
		'website' => [
				'name' => 'guanke',
				'title' => '观课',
				'login' => '/mobile/passport/login',
				'logout' => '/mobile/passport/logout',
				'home' => '/mobile/index/index'
		]
		 
];
