<?php
return [ 
		'account' => [ 
				'name' => '账号管理',
				'child' => [ 
						[ 
								'name' => '用户',
								'icon' => '/site/images/manage/icon/camera_white.png',
								'role' => [ 
										'supperadmin',
										'agent',
										'staff' 
								],
								'child' => [ 
										[ 
												'name' => '用户管理',
												'm' => 'admin',
												'c' => 'useraccount',
												'a' => 'index' 
										] 
								] 
						]
						,
						[ 
								'name' => '员工',
								'icon' => '/site/images/manage/icon/camera_white.png',
								'role' => [ 
										'supperadmin',
										'agent' 
								],
								'child' => [ 
										[ 
												'name' => '员工管理',
												'm' => 'admin',
												'c' => 'accountstaff',
												'a' => 'index' 
										] 
								] 
						] 
				] 
		] 
];