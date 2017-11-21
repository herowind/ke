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
// | 直播课程模型
// +----------------------------------------------------------------------
namespace app\guanke\model;

use app\manage\model\CommonMod;

class GuankeLivecourse extends CommonMod
{
    public function getMembervisibilitytextAttr($value,$data){
        $types = config('data.camera.membervisibility');
        return $types[$data['membervisibility']];
    }
    
    public function getProcessAttr($value,$data){
    	$starttimer = strtotime($data['starttime']);
    	$endtimer = strtotime($data['endtime']);
    	return $this->formatStartTime($starttimer,$endtimer);
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
    
    
    public function getEndtimetextAttr($value){
    	$time = strtotime($value);
    	return $this->formatEndTime($time);
    }
    
    //课程转换成数组
    public function getCourseAttr($value){
        if($value){
            return json_decode($value,true);
        }
        return [];
    }
    
    //老师转换成数组
    public function getTeacherAttr($value){
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
    
    protected function formatStartTime($starttimer,$endtimer)
    {
    	$startdiff = $_SERVER['REQUEST_TIME'] - $starttimer;
    	$startday = floor($startdiff / 86400);
    	$startfree = $startdiff % 86400;
    
    	if($startdiff < 0){
    		//尚未开始
    		if(abs($startday) == 0){
    			$rtnData = ['status'=>'ready','time'=>"今天 ".date('H:s 即将开始',$starttimer),'button'=>'报名中'];
    			return $rtnData;
    		}
    		if(abs($startday) == 1){
    			$rtnData = ['status'=>'ready','time'=>"明天 ".date('H:s 准备开始',$starttimer),'button'=>'报名中'];
    			return $rtnData;
    		}
    		$rtnData = ['status'=>'ready','time'=>date('m-d H:i 尚未开始',$starttimer),'button'=>'报名中'];
    		return $rtnData;
    	}else{
    		//如果已经开始了，判断是否结束
    		$enddiff = $_SERVER['REQUEST_TIME'] - $endtimer;
    		$endday = floor($enddiff / 86400);
    		$endfree = $enddiff % 86400;
    		if($enddiff < 0){
    			$rtnData = ['status'=>'start','time'=>'直播开始啦','button'=>'直播中'];
    			return $rtnData;
    		}else{
    			$rtnData = ['status'=>'finish','time'=>date('m-d H:i',$endtimer),'button'=>'已结束'];
    			return $rtnData;
    		}
    	}
    }
    
}