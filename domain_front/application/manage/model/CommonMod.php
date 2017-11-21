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
// | 基础模型
// +----------------------------------------------------------------------
namespace app\manage\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\manage\service\ManagerLoginSvc;

class CommonMod extends Model
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;

    public function scopeManage($query)
    {
        $cid = ManagerLoginSvc::getCid();
        $query->where('cid', '=', $cid);
    }
    
    public function scopeKeywords($query, $keywords)
    {
        if (empty($keywords[1])) {
            return;
        }
        $query->where($keywords[0], 'like', '%' . $keywords[1] . '%');
    }    
        
    protected function formatTime($timer,$formater='Y-m-d')
    {
        $str = '';
        $diff = $_SERVER['REQUEST_TIME'] - $timer;
        $day = floor($diff / 86400);
        $free = $diff % 86400;
        
        if($diff < 0){
        	if(abs($day) == 0){
        		return "今天 ".date('H:s',$timer);
        	}
        	if(abs($day) == 1){
        		return "明天 ".date('H:s',$timer);
        	}
        	if(abs($day) == 2){
        		return "后天 ".date('H:s',$timer);
        	}
        	
        	return date('m-d H:i',$timer);
        }
        
        if ($day > 0) {
            if($day<10){
                return $day . "天前";
            }
            return date($formater,$timer);
            
        } else {
            if ($free > 0) {$
                $hour = floor($free / 3600);
                $free = $free % 3600;
                if ($hour > 0) {
                    return $hour . "小时前";
                } else {
                    if ($free > 0) {
                        $min = floor($free / 60);
                        $free = $free % 60;
                        if ($min > 0) {
                            return $min . "分钟前";
                        } else {
                            if ($free > 0) {
                                return $free . "秒前";
                            } else {
                                return '刚刚';
                            }
                        }
                    } else {
                        return '刚刚';
                    }
                }
            } else {
                return '刚刚';
            }
        }
    }
}