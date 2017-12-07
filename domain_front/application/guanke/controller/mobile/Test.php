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
// | 直播课程页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeLivecourse;
use app\guanke\model\GuankeLivecoursemember;
use app\zhibo\model\ZhiboCamera;
use app\wechat\model\WechatUsertemplate;
use app\guanke\model\GuankeCourse;
use app\manage\model\UserTrade;
use app\manage\model\User;

class Test extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 直播课程列表页
	 */
	public function index(){
		$list = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch (); 
	}
	
	/**
	 * 直播课程详细页
	 */
	public function detail(){
		$live_id = $this->request->param('live_id');
		$this->initMember();
		$wechat = $this->getQrcode();
		$this->assign('live_id',$live_id);
		$this->assign('wechat',$wechat);
		return $this->fetch ();
	}
	
	/**
	 * 直播课程列表数据
	 */
	public function listdata(){
		$list = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$list->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	/**
	 * 直播课程+普通课程列表数据
	 */
	public function alllistdata(){
		$livecourseList = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$livecourseList->append(['process'])->toArray();
		
		$courseList = GuankeCourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		
		return ['code'=>1,'msg'=>'查询成功','data'=>['livecourseList'=>$livecourseList,'courseList'=>$courseList]];
	}
	
	public function detaildata(){
		$live_id = $this->request->param('live_id');
		$this->initMember();
		$this->initOfficialAccount();
		$detail = GuankeLivecourse::find($live_id);
		$detail->append(['process'])->toArray();
		$detail->member = (object)['issubscribe'=>0,'isfavor'=>0,'isveryfy'=>0,'url'=>''];
		//①判断是否需要报名
		if($detail->membervisibility != 1){
			$courseMember = GuankeLivecoursemember::where('live_id',$detail->id)->where('member_id',$this->getMid())->find();
			if(empty($courseMember)){
				//需报名
				$detail->member->isfavor = 0;
				return ['code'=>0,'msg'=>'您尚未报名','error'=>'unfavor','data'=>$detail];
			}else{
				$detail->member->isfavor = $courseMember['isfavor'];
				$detail->member->isveryfy = $courseMember['isveryfy'];
				if($detail->member->isveryfy !=1){
					return ['code'=>0,'msg'=>'已报名成功,审核中请稍等','error'=>'unveryfy','data'=>$detail];
				}
			}
		}
		
		//②判断是否强制关注公众号
		if($detail->issubscribe == 1){
			//验证是否关注,没有关注弹出二维码
			$user = $this->officialAccount->user->get($this->getOpenid());
			$detail->member->issubscribe = $user['subscribe'];
			if($detail->member->issubscribe !=1){
				return ['code'=>0,'msg'=>'您尚未关注公众平台，无法播放','error'=>'unsubscribe','data'=>$detail];
			}
		}
		//③判断是否到时间
		if(in_array($detail['process']['status'], ['ready','finish'])){
			$detail->member->url = '';
		}else{
			$detail->member->url = ZhiboCamera::where('id',$detail->camera_id)->value('url');
		}
		
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 报名观看
	 */
	public function favor(){
		//验证登录
		$this->initMember();
		//获取直播详情
		$live_id = $this->request->param('live_id');
		$detail = GuankeLivecourse::find($live_id);
		//验证是否报过名
		$courseMember = GuankeLivecoursemember::where('live_id',$live_id)->where('member_id',$this->getMid())->find();
		if(empty($courseMember)){
			//未报过名，进行报名
			$data = [
					'live_id' => $live_id,
					'member_id'=>$this->getMid(),
					'cid'=>$this->getCid(),
					'openid'=>$this->getOpenid(),
					'nickname'=>$this->member->nickname,
					'mobile'=>$this->member->mobile,
					'isfavor'=>1,
					'isveryfy'=>$detail->membervisibility == 2 ? 1 : 0,
			];
			$courseMember = GuankeLivecoursemember::create($data);	
			if($courseMember){
				$template = WechatUsertemplate::where('cid',$this->getCid())->where('short_id','TM00080')->find();
				if($template){
					//设置模板
					$this->initOfficialAccount();
					$this->officialAccount->template_message->send([
	    				'touser' => $this->getOpenid(),
	    				'template_id' => $template['template_id'],
	    				'url' => APP_SITE."/guanke/mobile.livecourse/detail.html?school_id={$this->getSchoolId()}&live_id={$live_id}",
	    				'data' => [
	    						'userName' => ['value'=>$this->member->nickname?:'课官','color'=>'#0033cc'] ,
	    						'courseName' => ['value'=>'●'.$detail->name,'color'=>'#0033cc'],
	    						'date' => ['value'=>$detail->starttime,'color'=>'#0033cc'] ,
	    						'remark' => ['value'=>'课程很精彩，请点击查看详情☞','color'=>'#ff3333'],
	    						
	    				],
	    			]);
				}
				
			}
			
		}
		//判断是否审核通过
		if($courseMember->isveryfy==1){
			$url = ZhiboCamera::where('id',$detail->camera_id)->value('url');
			return ['code'=>1,'msg'=>'已报名成功，可以观看了','url'=>$url];
		}else{
			return ['code'=>0,'msg'=>'已报名成功,审核中请稍等','error'=>'unveryfy'];
		}
	}
	
	/**
	 * 开始播放
	 */
	public function startplay(){
		$this->initMember();
		//验证是否付费
		$detail = UserTrade::where('member_id',$this->getMid())->where('validdate',date('Y-m-d'))->find();
		$live = GuankeLivecourse::field('id,name,camera_id')->where('id',$this->request->param('live_id'))->find();
		if(empty($detail)){
			$price = config('manage.zhiboprice.live');
			//执行扣费
			$data = [
					'cid' => $this->getCid(),
					'member_id' => $this->getMid(),
					'goodstype' => 'livecourse',
					'goodsinfo' => json_encode($live,JSON_UNESCAPED_UNICODE),
					'type'		=> '1',
					'price'		=> $price,
					'validdate'	=> date('Y-m-d'),	
			];
			$detail = UserTrade::create($data);
			if($detail){
				//操作成功，主账户扣费
				User::where('id',$this->getCid())->update(['amount' => ['exp',"amount-{$price}"]]);
			}
		}
		return ['code'=>1,'msg'=>'操作成功'];
	}

}