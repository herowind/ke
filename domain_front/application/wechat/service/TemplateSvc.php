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
// | 管理者登录控制器
// +----------------------------------------------------------------------
namespace app\wechat\service;

use think\Db;
use EasyWeChat\Factory;

class TemplateSvc
{
	public static function execTask(){
		$currentTime = time();
		$openPlatform = Factory::openPlatform(config('wechat.component'));
		$list = Db::table('app_wechat_usertemplatetask')->where('issend',0)->where('totime','<=' ,$currentTime)->select();
		foreach ($list as $task){
			//锁定
			$flag = Db::table('app_wechat_usertemplatetask')->where('issend',0)->where('id',$task['id'])->setField('issend', 1);
			if($flag == 1){
				switch($task['totype']){
					case 'group':
						self::groupSend($openPlatform, $task);
						break;
					case 'openid':
						self::openidSend($openPlatform, $task);
						break;
					case 'model':
						self::modelSend($openPlatform, $task);
						break;
				}
				//完成
				Db::table('app_wechat_usertemplatetask')->where('issend',1)->where('id',$task['id'])->setField('issend', 2);
			}
		}
	}
	/**
	 * 群发消息
	 * @param unknown $openPlatForm
	 * @param unknown $task
	 */
    public static function groupSend($openPlatform,$task)
    {
    	$officialAccount = $openPlatform->officialAccount($task['appid'], $task['authorizer_refresh_token']);
    	//准备数据
    	$msgData = [];
    	$form = json_decode($task['form'],true);
    	foreach ($form as $val){
    		$msgData[$val['k']] = $val['v'];
    	}
		//开始发送
    	if($task['touser'] == '0'){
    		//全部发送
    		$hasNext = true;
    		$nextOpenId = '';
    		$tag = $officialAccount->user;
    		do{
    			$rtnData = $tag->list($nextOpenId);
    			if($rtnData['count']>0){
    				foreach ($rtnData['data']['openid'] as $val){
    					$officialAccount->template_message->send([
    							'touser' => $val,
    							'template_id' => $task['template_id'],
    							'url' => $task['tourl'],
    							'data' => $msgData,
    					]);
    				}
    				$nextOpenId = $rtnData['next_openid'];
    			}else{
    				$hasNext = false;
    			}
    		}while($hasNext);
    	}else{
    		//指定标签
    		$hasNext = true;
    		$nextOpenId = '';
    		$tag = $officialAccount->user_tag;
    		do{
    			$rtnData = $tag->usersOfTag($task['touser'], $nextOpenId);
    			if($rtnData['count']>0){
    				foreach ($rtnData['data']['openid'] as $val){
    					$officialAccount->template_message->send([
    							'touser' => $val,
    							'template_id' => $task['template_id'],
    							'url' => $task['tourl'],
    							'data' => $msgData,
    					]);
    				}
    				$nextOpenId = $rtnData['next_openid'];
    			}else{
    				$hasNext = false;
    			}
    		}while($hasNext);
    		 
    	}	
    }
    
    /**
     * 发送给个人
     * @param unknown $openPlatForm
     * @param unknown $task openid,openid,openid
     */
    public static function openidSend($openPlatform,$task){
    	$officialAccount = $openPlatform->officialAccount($task['appid'], $task['authorizer_refresh_token']);
    	//准备数据
    	$msgData = [];
    	$form = json_decode($task['form'],true);
    	foreach ($form as $val){
    		$msgData[$val['k']] = ['value'=>$val['v'],'color'=>$val['c']];
    	}
    	$openids = explode(',', $task['touser']);
    	foreach ($openids as $val){
    		$officialAccount->template_message->send([
    				'touser' => $val,
    				'template_id' => $task['template_id'],
    				'url' => $task['tourl'],
    				'data' => $msgData,
    		]);
    	}

    }
    
    /**
     * 根据模型发送
     * @param unknown $openPlatForm
     * @param unknown $task  modelname|field|value  该modelname必须有openid字段
     */
    public static function modelSend($openPlatform,$task){
    	$officialAccount = $openPlatform->officialAccount($task['appid'], $task['authorizer_refresh_token']);
    	//准备数据
    	$msgData = [];
    	$form = json_decode($task['form'],true);
    	foreach ($form as $val){
    		$msgData[$val['k']] = ['value'=>$val['v'],'color'=>$val['c']];
    	}
    	$model = explode('|', $task['touser']);
    	$total = model($model[0])->where($model[1],$model[2])->count();
    	if($total>0){
    		//暂时没有分段查询，一次性查询出来
    		$list = model($model[0])->where($model[1],$model[2])->column('openid');
    		foreach ($list as $val){
    			if($val){
    				$officialAccount->template_message->send([
    						'touser' => $val,
    						'template_id' => $task['template_id'],
    						'url' => $task['tourl'],
    						'data' => $msgData,
    				]);
    			}
    		}
    	}
    }

}
