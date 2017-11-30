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
// | 观课数据字典设置
// +----------------------------------------------------------------------
return [
    'uploadfolder'=>'school',
    'school' => [
        'type' => [
            '1' => '主校',
            '2' => '分校'
        ]
    ],
    'course' => [
        'type' => [
            '1' => '其他',
            '2' => '兴趣课',
            '3' => '文化课',
            '4' => '外语课',
        ],
        'feetype' => [
            '1' => '免费',
            '2' => '收费'
        ],
        'isopen' => [
            '0' => '未开始',
            '1' => '直播中'
        ], 
    ],
    'camera' => [
        'membervisibility' => [
            '1' => '无限制',
            '2' => '报名-自动审核',
            '3' => '报名-人工审核',
        ],
    	'issubscribe'=>[
    			'0' => '无需关注',
    			'1' => '必须关注'
    	]
    ],
	
	'timesection' =>[
		['o' => '08:00','c' => '18:00', 'w' => '星期一','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期二','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期三','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期四','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期五','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期六','is'=>1],
		['o' => '08:00','c' => '18:00', 'w' => '星期日','is'=>1],
		
	],
];
