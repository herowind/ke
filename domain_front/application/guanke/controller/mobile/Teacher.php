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
// | 教师页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeContentpage;
use app\guanke\model\GuankeTeacher;
use app\manage\model\UserMember;

class Teacher extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 教师首页面
	 */
	public function index(){
		$list = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch ();
	}
	
	/**
	 * 教师详细页面
	 */
	public function detail(){
		$detail = GuankeTeacher::get($this->request->param('teacher_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	/**
	 * 教师详细ajax
	 */
	public function detaildata(){
		$detail = GuankeTeacher::get($this->request->param('teacher_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 教师列表页
	 */
	public function listdata(){
		$list = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	/**
	 * 教师绑定页面
	 */
	public function bindteacher(){
		session(null);
		//判断是否绑定了公众号
		$this->initOfficialAccount();
		if(empty($this->wechat)){
			$this->error('未绑定公众号，无法添加教师','/guanke/mobile.home/index');
		}
		//判断用户是否关注公众号
		$this->initMember();
		//判断是否已是老师
		$teacher = GuankeTeacher::where('cid',$this->getCid())->where('member_id',$this->getMid())->find();
		if($teacher){
			if($teacher['isveryfy'] ==1){
				$this->success('您已审核通过','/guanke/mobile.home/index');
			}else{
				$this->success('您的申请正在审核中','/guanke/mobile.home/index');
			}
		}else{
			$memberDetail = $this->officialAccount->user->get($this->getOpenid());
			if($memberDetail['subscribe'] != 1){
				$this->error('请先关注公众号','/guanke/mobile.home/index');
			}
		}
		$this->assign('member',$memberDetail);
		$this->fetch();
	}
	
	
	public function dobindteacher(){
		$this->initMember();
		//判断是否已是老师
		$teacher = GuankeTeacher::where('cid',$this->getCid())->where('member_id',$this->getMid())->find();
		if($teacher){
			if($teacher['isveryfy'] ==1){
				$this->success('您已审核通过');
			}else{
				$this->success('您的申请正在审核中');
			}
		}
		$memberDetail = $this->officialAccount->user->get($this->getOpenid());
		if($memberDetail['subscribe'] == 1){
			//已关注，已取到详细信息
			$member = UserMember::where('openid',$this->getOpenid());
			$mobile = $this->request->param('mobile');
			if(!preg_match("/^1[2345678]{1}\d{9}$/",$mobile)){
				$this->error('手机号码不正确');
			}

			//判断是否保存了nickname
			if(empty($member->nickname)){
				$member->nickname = $memberDetail['nickname'];
				$member->gender = $memberDetail['sex'];
				$member->avatar = $memberDetail['headimgurl'];
				$member->province = $memberDetail['province'];
				$member->city = $memberDetail['city'];
			}
			$member->mobile = $mobile;
			$member->save();
			//创建老师
			$data = [
					'cid' => $this->getCid(),
					'member_id' => $this->getMid(),
					'avatar' =>$memberDetail['headimgurl'],
					'name' =>$memberDetail['nickname'],
					'mobile' =>$mobile,
			];
			$teacher = GuankeTeacher::create($data);
			$this->success('您的申请已提交','/guanke/mobile.home/index');
		}else{
			//未关注，提示去关注公众号
			$this->error('请先关注公众号');
		}
	}
}