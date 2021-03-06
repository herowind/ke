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
use app\guanke\model\GuankeLivemember;
use app\wechat\service\TemplateSvc;
use think\Db;

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
		//session(null);
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
		return $this->fetch();
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
		$this->initOfficialAccount();
		$memberDetail = $this->officialAccount->user->get($this->getOpenid());
		if($memberDetail['subscribe'] == 1){
			//已关注，已取到详细信息
			$member = UserMember::where('cid',$this->getCid())->where('openid',$this->getOpenid())->find();
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
					'openid' =>$this->getOpenid(),
					'mobile' =>$mobile,
					'isdisplay' =>0,
					'isveryfy' =>0,
			];
			$teacher = GuankeTeacher::create($data);
			$this->success('您的申请已提交','/guanke/mobile.home/index');
		}else{
			//未关注，提示去关注公众号
			$this->error('请先关注公众号');
		}
	}
	
	public function authmember(){
		$this->initMember();
		return $this->fetch();
	}
	
	/**
	 * 查看审核信息
	 */
	public function authmemberlist(){
		$this->initMember();
		$params = $this->request->param();
		$teacher = GuankeTeacher::where('cid',$this->getCid())->where('openid',$this->getOpenid())->find();
		if(empty($teacher) || empty($teacher['isreceive'])){
			$this->error('无权查看');
		}
		$pageData = GuankeLivemember::where('cid',$this->getCid())->where('isveryfy',$params['isveryfy'])->order('create_time desc')->paginate(20);
		return $pageData;
	}
	/**
	 * 授权信息
	 */
	public function doauth(){
		$this->initMember();
		$params = $this->request->param();
		$teacher = GuankeTeacher::where('cid',$this->getCid())->where('openid',$this->getOpenid())->find();
		if(empty($teacher) || empty($teacher['isreceive'])){
			$this->error('无权审核');
		}
		
		$member = GuankeLivemember::where('cid',$this->getCid())->where('id',$params['id'])->find();
		$member->isveryfy = $params['isveryfy'];
		$member->save();
		if(empty($member->isveryfynotice)){
			$this->authWxnotice($member);
			GuankeLivemember::where('cid',$this->getCid())->where('id',$params['id'])->update(['isveryfynotice' => 1]);
		}
		return ['code'=>1,'msg'=>'操作成功'];
	}
	
	/**
	 * 删除信息
	 */
	public function doauthdelete(){
		$this->initMember();
		$params = $this->request->param();
		$teacher = GuankeTeacher::where('cid',$this->getCid())->where('openid',$this->getOpenid())->find();
		if(empty($teacher) || empty($teacher['isreceive'])){
			$this->error('无权审核');
		}
		$member = GuankeLivemember::where('cid',$this->getCid())->where('id',$params['id'])->find();
		$member->delete(true);
		return ['code'=>1,'msg'=>'操作成功'];
	}
	
	
	/**
	 * 发送微信报名通知
	 * @param unknown $live
	 */
	protected function authWxnotice($member){
		$this->initOfficialAccount();
		switch($member->livetype){
			case 'livecourse':
				$live['livetypetext'] = '课程报名';
				break;
			case 'liveschool':
				$live['livetypetext'] = '看护申请';
				break;
			case 'liveactive':
				$live['livetypetext'] = '活动报名';
				break;
			default:
				return ['code'=>0,'msg'=>'信息获取失败,请重新操作'];
		}
		
		//如果绑定了微信公众号，则发送通知
		$task['form'] = [
				['k'=>'first',    't'=>'通知信息',  'v'=>"您好，您的请求已受理完毕",    'c'=>'#000'],
				['k'=>'keyword1', 't'=>'审核内容',  'v'=>"[{$live['livetypetext']}]".$member->live_name,                			'c'=>'#0033cc'],
				['k'=>'keyword2', 't'=>'审核结果',  'v'=>$member->isveryfy==1?'审核通过':'审核拒绝',           'c'=>'#0033cc'],
				['k'=>'remark',   't'=>'备注',      'v'=>'请点击查看详情☞', 'c'=>'#ff3333'],
		];
		$task['template_id'] = TemplateSvc::getTemplateIdByCid($member->cid, 'OPENTM401683926');
		$task['touser'] = $member->openid;
		$task['tourl'] = APP_SITE."/guanke/mobile.{$member->livetype}/detail.html?sid={$member->school_id}&live_id={$member->live_id}";
		if($task['template_id']){
			TemplateSvc::openidSend($this->officialAccount, $task);
		}
	}
	
}