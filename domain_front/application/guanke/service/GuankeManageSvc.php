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
// | 管理业务
// +----------------------------------------------------------------------
namespace app\guanke\service;

use app\guanke\model\GuankeSchool;
use app\zhibo\model\ZhiboCamera;
use app\guanke\model\GuankeCourse;
use app\guanke\model\GuankeTeacher;

class GuankeManageSvc
{
    //获取学校信息用于关联存储
    public static function getSchoolJson($id){
        $data = GuankeSchool::field('name,intro,logo,banner')->manage()->find($id);
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    
    //获取课程信息用于关联存储
    public static function getCourseJson($id){
        $data = GuankeCourse::field('name,intro,banner')->manage()->find($id);
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    
    //获取老师信息用于关联存储
    public static function getTeacherJson($id){
        $data = GuankeTeacher::field('name,intro,avatar')->manage()->find($id);
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    
    //获取摄像头信息用于关联存储
    public static function getCameraJson($id){
        $data = ZhiboCamera::field('name,code,url')->manage()->find($id);
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    
    
}