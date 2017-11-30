<?php
return [ 
		'guanke' => [ 
				'name' => '管理',
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
						],
						
						[ 
								'name' => '公众号',
								'icon' => '/site/images/manage/icon/wechat_white.png',
								'child' => [ 
										[ 
												'name' => '活跃会员',
												'm' => 'wechat',
												'c' => 'manage.member',
												'a' => 'index' 
										],
										[
												'name' => '模板消息',
												'm' => 'wechat',
												'c' => 'manage.template',
												'a' => 'index'
										],
										[ 
												'name' => '绑定公众号',
												'm' => 'wechat',
												'c' => 'manage.setting',
												'a' => 'index' 
										] 
								] 
						] 
				] 
		] 
];