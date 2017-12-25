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
// | 直播基础控制器
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeLivemember;
use app\guanke\model\GuankeLivecourse;
use app\guanke\model\GuankeLiveschool;
use app\guanke\model\GuankeLiveactive;
use app\wechat\service\TemplateSvc;
use app\zhibo\model\ZhiboCamera;
use app\manage\service\UserTradeSvc;
use app\guanke\model\GuankeTeacher;

abstract class LiveController extends SchoolController {
	//直播类型 livecourse,liveactive,liveschool;
	protected $livetype = '';
	
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 获得直播链接
	 * @return number[]|string[]
	 */
	public function videoinfo(){
		$live = $this->getLive();
		//验证权限
		$rtnData = $this->checkmembervisibility($live);
		if($rtnData['code'] == 1){
			//验证是否到播放时间
			$rtnData = $this->checktime($live);
			if($rtnData['code'] == 1){
				$rtnData['video']['url'] = ZhiboCamera::where('id',$live->camera_id)->value('url');
			}
		}
		return $rtnData;
	}
	
	/**
	 * 开始播放
	 */
	public function videoplay(){
		$this->initMember();
		$params = $this->request->only(['live_id']);
		$live = $this->getLive();
		$live['type'] = $live['livetype'];
		$rtnData = UserTradeSvc::memberPayDaikou($this->getCid(),$this->getMid(), $live);
		return $rtnData;
	}
	
	/**
	 * 用户直播报名
	 */
	public function favor(){
		//页面参数 live_id,
		$params = $this->request->only(['live_id','mobile']);
		$live = $this->getLive();
		//验证权限
		$rtnData = $this->checkmembervisibility($live);
		if($rtnData['code'] == 0 && $rtnData['error'] == 'unfavor'){
			$live['livetype'] = $this->livetype;
			$live['school']   = $this->school;
			$live['member']   = $this->member;
			
			//未报过名，进行报名
			$mobile = $params['mobile']?:$live['member']->mobile;
			$data = [
					'livetype'		=> $live['livetype'],
					'live_id' 		=> $live['id'],
					'cid'			=> $live['member']->cid,
					'member_id'		=> $live['member']->id,
					'openid'		=> $live['member']->openid,
					'nickname'		=> $live['member']->nickname,
					'avatar'		=> $live['member']->avatar,
					'mobile'		=> $params['mobile']?:$live['member']->mobile,
					'isfavor'		=> 1,											//报名成功
					'isveryfy'		=> $live['membervisibility'] == 3 ? 0 : 1,		//是否需要审核
			];
			$enrollMember = GuankeLivemember::create($data);
			if($enrollMember){
				//报名成功，发送报名
				$this->favorWxnotice($live);
			}
			
			if($enrollMember->isveryfy==1){
				$rtnData = ['code'=>1,'msg'=>'已报名成功'];
			}else{
				$rtnData = ['code'=>0,'msg'=>'已报名成功,审核中请稍等','error'=>'unveryfy'];
			}
		}
		return $rtnData;
	}
	
	
	
	/**
	 * 获得直播信息
	 */
	protected function getLive(){
		//获得直播信息
		$params = $this->request->only(['live_id']);
		switch($this->livetype){
			case 'livecourse':
				$field='id,name,camera_id,membervisibility,starttime,endtime';
				$liveMod = new GuankeLivecourse();
				break;
			case 'liveschool':
				$field='id,name,camera_id,membervisibility,timesection';
				$liveMod = new GuankeLiveschool();
				break;
			case 'liveactive':
				$field='id,name,camera_id,membervisibility,starttime,endtime';
				$liveMod = new GuankeLiveactive();
				break;
			default:
				return ['code'=>0,'msg'=>'信息获取失败,请重新操作'];
		}
		$live = $liveMod->field($field)->where('id',$params['live_id'])->find();
		$live['livetype'] = $this->livetype;
		return $live;
	}	
	
	
	/**
	 * 验证是否报名
	 * @param unknown $live
	 */
	protected function checkmembervisibility($live){		
		//需要报名
		if($live['membervisibility'] != 1){
			//获取登录用户信息
			$this->initMember();
			$member = $this->member;
			//报名验证
			$enrollMember = GuankeLivemember::where('livetype',$this->livetype)->where('live_id',$live['id'])->where('member_id',$member->id)->find();
			if($enrollMember){
				//判断是否审核通过
				if($enrollMember->isveryfy==1){
					return ['code'=>1,'msg'=>'已报名成功'];
				}else{
					return ['code'=>0,'msg'=>'已报名成功,审核中请稍等','error'=>'unveryfy'];
				}
			}else{
				return ['code'=>0,'msg'=>'您尚未报名','error'=>'unfavor'];
			}
		}
		return ['code'=>1,'msg'=>'验证通过'];
	}
	
	protected abstract function checktime($live);

	
	/**
	 * 发送微信报名通知
	 * @param unknown $live
	 */
	protected function favorWxnotice($live){
		$this->initOfficialAccount();
		//如果绑定了微信公众号，则发送通知
		
		if($this->officialAccount){
			switch($live['livetype']){
				case 'livecourse':
					$templateShortId = 'TM00080';
					$authtype='课程申请';
					$task['form'] = [
						['k'=>'userName',   't'=>'用户名',    'v'=>$live['member']->nickname?:'课官',    'c'=>'#0033cc'],
						['k'=>'courseName', 't'=>'课程名称',  'v'=>$live['name'],                'c'=>'#0033cc'],
						['k'=>'date',       't'=>'开课日期',  'v'=>$live['starttime'],           'c'=>'#0033cc'],
						['k'=>'remark',     't'=>'备注',      'v'=>'课程很精彩，请点击查看详情☞', 'c'=>'#ff3333'],
					];
					break;
				case 'liveschool':
					$templateShortId = 'TM00080';
					$authtype='看护申请';
					$task['form'] = null;
					break;
				case 'liveactive':
					$templateShortId = 'TM00080';
					$authtype='活动申请';
					$task['form'] = null;
					break;
			}
			$task['template_id'] = TemplateSvc::getTemplateIdByCid($live['school']->cid, $templateShortId);
			$task['touser'] = $live['member']->openid;
			$task['tourl'] = APP_SITE."/guanke/mobile.{$live['livetype']}/detail.html?sid={$live['school']->cid}&live_id={$live['id']}";
			if($task['template_id']){
				TemplateSvc::openidSend($this->officialAccount, $task);
			}
			//需要审核，则通知管理员
			if($live['membervisibility'] == 3){
				$openids = GuankeTeacher::where('cid',$this->getCid())->where('isreceive',1)->column('openid');
				$task['form'] = [
						['k'=>'first',   	't'=>'头部信息',  'v'=>'有个新的申请等待审核（'.$live['member']->nickname?:'新客户）',    'c'=>'#0033cc'],
						['k'=>'keyword1', 	't'=>'申请日期',  'v'=>date('Y-m-d H:s'),                'c'=>'#0033cc'],
						['k'=>'keyword2',   't'=>'申请主题',  'v'=>$authtype,           			'c'=>'#0033cc'],
						['k'=>'keyword3',   't'=>'申请内容',  'v'=>$live['name'], 'c'=>'#ff3333'],
						['k'=>'remark',     't'=>'备注信息',  'v'=>'点击进行审核☞', 'c'=>'#ff3333'],
				];
				$task['template_id'] = TemplateSvc::getTemplateIdByCid($live['school']->cid, 'OPENTM413025199');
				$task['touser'] = $openids;
				$task['tourl'] = APP_SITE."/guanke/mobile.teacher/authmemberlist.html?sid={$live['school']->cid}&isveryfy=0";
			}
		}
	}
	
}