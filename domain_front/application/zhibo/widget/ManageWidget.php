<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究  )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 下拉选择模型
// +----------------------------------------------------------------------

namespace app\zhibo\widget;

use app\zhibo\model\ZhiboCamera;

class ManageWidget{
    /**
     * 摄像头选择
     * @param string $selected
     */
	public function cameraOptions($selected=''){
		$options = '';
		$list = ZhiboCamera::manage()->limit(50)->order('sort desc')->select();
		foreach ($list as $val){
			if($val['id'] != $selected){
				$options.='<option value="';
			}else{
				$options.='<option selected value="';
			}
			$options.=$val['id'];
			$options.='">';
			$options.=$val['name'];
			$options.='</option>';
		}
		return $options;
	}
}