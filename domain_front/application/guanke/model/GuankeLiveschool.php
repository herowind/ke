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
// | 学校监控模型
// +----------------------------------------------------------------------
namespace app\guanke\model;

use app\manage\model\CommonMod;

class GuankeLiveschool extends CommonMod
{
    public function getMembervisibilitytextAttr($value,$data){
        $types = config('data.camera.membervisibility');
        return $types[$data['membervisibility']];
    }
    
    public function getApplyAttr($value,$data){
    	 
    	switch($data['membervisibility']){
    		case 2:
    			return ['name'=>'立即关注','content'=>$data['intro']?$data['intro']:'请关注公众号后，免费观看课程'];
    			break;
    		case 3:
    			return ['name'=>'立即申请','content'=>$data['intro']?$data['intro']:'申请审核通过后才能观看课程，如有疑问请拨打客服电话'];
    			break;
    		case 4:
    			return ['name'=>'微信支付','content'=>"本课程为收费课程，需付费观看"];
    			break;
    		default:
    			return ['name'=>'立即申请','content'=>'如有疑问请拨打客服电话'];
    	}
    }
    
    //学校转换成数组
    public function getSchoolAttr($value){
        if($value){
            return json_decode($value,true);
        }
        return [];
    }
    
    //摄像头转换成数组
    public function getCameraAttr($value){
        if($value){
            return json_decode($value,true);
        }
        return [];
    }
}