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

namespace app\guanke\widget;

use app\guanke\model\GuankeSchool;
use app\guanke\model\GuankeCourse;
use app\guanke\model\GuankeTeacher;

class ManageWidget{
    /**
     * 学校选择
     * @param string $selected
     */
	public function schoolOptions($selected=''){
		$options = '';
		$list = GuankeSchool::manage()->limit(50)->order('sort desc')->select();
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
	
	/**
	 * 课程选择
	 * @param string $selected
	 */
	public function courseOptions($selected=''){
	    $options = '';
	    $list = GuankeCourse::manage()->limit(50)->order('sort desc')->select();
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
	
	/**
	 * 老师选择
	 * @param string $selected
	 */
	public function teacherOptions($selected=''){
	    $options = '';
	    $list = GuankeTeacher::manage()->limit(50)->order('sort desc')->select();
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