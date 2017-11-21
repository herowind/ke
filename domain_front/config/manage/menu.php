<?php
return [
    'guanke' => [
        'name' => '观课',
        'child' => [
            [
                'name' => '直播',
                'icon' => '/site/images/manage/icon/camera_white.png',
                'child' => [
                    [
                        'name' => '课程直播',
                        'm' => 'guanke',
                        'c' => 'manage.livecourse',
                        'a' => 'index'
                    ],
                    
                    [
                        'name' => '学校监控',
                        'm' => 'guanke',
                        'c' => 'manage.liveschool',
                        'a' => 'index'
                    ],
                    [
                        'name' => '活动直播',
                        'm' => 'guanke',
                        'c' => 'manage.liveactivity',
                        'a' => 'index'  
                    ],
                    [
                        'name' => '摄像头管理',
                        'm' => 'zhibo',
                        'c' => 'manage.camera',
                        'a' => 'index'
                    ]
                ]
            ],
            [
                'name' => '学校',
                'icon' => '/site/images/manage/icon/school_white.png',
                'child' => [
                    [
                        'name' => '学校管理',
                        'm' => 'guanke',
                        'c' => 'manage.school',
                        'a' => 'index'
                    ],
                    [
                        'name' => '课程管理',
                        'm' => 'guanke',
                        'c' => 'manage.course',
                        'a' => 'index'
                    ],
                    [
                        'name' => '老师管理',
                        'm' => 'guanke',
                        'c' => 'manage.teacher',
                        'a' => 'index'
                    ]
                ]
            ]
        ]
        
    ]
    ,
    'wechat' => [
        'name' => '微信',
        'child' => [
            [
                'name' => '会员',
                'icon' => '/site/images/manage/icon/member_white.png',
                'child' => [
                    [
                        'name' => '活跃会员',
                        'm' => 'wechat',
                        'c' => 'manage.member',
                        'a' => 'index'
                    ]
                ]
            ],
            
            [
                'name' => '设置',
                'icon' => '/site/images/manage/icon/wechat_white.png',
                'child' => [
                    [
                        'name' => '微信公众号',
                        'm' => 'wechat',
                        'c' => 'manage.setting',
                        'a' => 'index'
                    ]
                ]
            ]
        ]
    ],
    'system' => [
        'name' => '系统',
        'child' => [
            [
                'name' => '设置',
                'child' => [
                    [
                        'name' => '微信设置',
                        'act' => 'publishList',
                        'op' => 'Weixinad',
                        'mod' => 'spread'
                    ],
                    [
                        'name' => '短信模板',
                        'act' => 'publishList',
                        'op' => 'Weixinad',
                        'mod' => 'spread'
                    ]
                ]
            ],
            [
                'name' => '会员',
                'child' => [
                    [
                        'name' => '会员列表',
                        'act' => 'index',
                        'op' => 'System',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '充值记录',
                        'act' => 'region',
                        'op' => 'Tools',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '提现申请',
                        'act' => 'navigationList',
                        'op' => 'System',
                        'mod' => 'system'
                    ]
                ]
            ],
            [
                'name' => '权限',
                'child' => [
                    [
                        'name' => '管理员列表',
                        'act' => 'index',
                        'op' => 'System',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '角色',
                        'act' => 'region',
                        'op' => 'Tools',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '权限资源列表',
                        'act' => 'navigationList',
                        'op' => 'System',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '管理员日志',
                        'act' => 'navigationList',
                        'op' => 'System',
                        'mod' => 'system'
                    ]
                ]
            ],
            [
                'name' => '数据',
                'child' => [
                    [
                        'name' => '数据备份',
                        'act' => 'index',
                        'op' => 'System',
                        'mod' => 'system'
                    ],
                    [
                        'name' => '数据还原',
                        'act' => 'region',
                        'op' => 'Tools',
                        'mod' => 'system'
                    ]
                ]
            ]
        ]
    ]
];