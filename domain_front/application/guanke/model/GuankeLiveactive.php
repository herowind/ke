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
// | 直播活动模型
// +----------------------------------------------------------------------
namespace app\guanke\model;

use app\manage\model\CommonMod;

class GuankeLiveactive extends CommonMod
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
    
    public function getEndtimetextAttr($value){
    	$time = strtotime($value);
    	return $this->formatEndTime($time);
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
    			$rtnData = ['status'=>'ready','time'=>"今天 ".date('H:s 即将开始',$starttimer),'button'=>'报名中','starttime'=>$starttimer];
    			return $rtnData;
    		}
    		if(abs($startday) == 1){
    			$rtnData = ['status'=>'ready','time'=>"明天 ".date('H:s 准备开始',$starttimer),'button'=>'报名中','starttime'=>$starttimer];
    			return $rtnData;
    		}
    		$rtnData = ['status'=>'ready','time'=>date('m-d H:i 尚未开始',$starttimer),'button'=>'报名中','starttime'=>$starttimer];
    		return $rtnData;
    	}else{
    		//如果已经开始了，判断是否结束
    		$enddiff = $_SERVER['REQUEST_TIME'] - $endtimer;
    		$endday = floor($enddiff / 86400);
    		$endfree = $enddiff % 86400;
    		if($enddiff < 0){
    			$rtnData = ['status'=>'start','time'=>'直播开始啦','button'=>'直播中','starttime'=>$starttimer];
    			return $rtnData;
    		}else{
    			$rtnData = ['status'=>'finish','time'=>date('m-d H:i',$endtimer),'button'=>'已结束','starttime'=>$starttimer];
    			return $rtnData;
    		}
    	}
    }
}