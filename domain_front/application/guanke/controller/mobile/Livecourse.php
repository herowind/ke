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

class Livecourse extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 直播课程详细页
	 */
	public function detail(){
		//验证是否登录
		$detail = GuankeLivecourse::find($this->request->param('id'));
		if($detail->membervisibility != 1){
			$this->initMember();
			$courseMember = GuankeLivecoursemember::where('livecourse_id',$detail->id)->where('member_id',$this->getMid())->find();
			if(empty($courseMember)){
				$data = [
						'livecourse_id' => $detail->id,
						'member_id'=>$this->getMid(),
						'cid'=>$this->getCid(),
						'isfavor'=>0,
						'islike'=>0,
				];
				GuankeLivecoursemember::create($data);
			}
			$detail['camera']['url'] = '#';
		}
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 直播课程列表页
	 */
	public function lists(){
		$list = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	/**
	 * 检查会员是否有权观看
	 */
	public function checkMembervisibility(){
		$this->initMember();
		$this->initOfficialAccount();
		$detail = GuankeLivecourse::find($this->request->param('id'));
		switch($detail->membervisibility){
			case 1:
				break;
			case 2:
				//验证是否关注,没有关注弹出二维码
				$user = $this->officialAccount->user->get($this->getOpenid());
				if($user['subscribe'] != 1){
					return ['code'=>0,'error'=>'unsubscribe','msg'=>'请先关注公众平台'];
				}
				break;
			case 3:
				//验证是否审核，审核通过
				$courseMember = GuankeLivecoursemember::where('livecourse_id',$detail->id)->where('member_id',$this->getMid())->find();
				if($courseMember->isfavor != 1){
					return ['code'=>0,'error'=>'unfavor','msg'=>'您尚未报名'];
				}
				if($courseMember->isveryfy == 0){
					return ['code'=>0,'error'=>'unveryfy','msg'=>'请耐心等待审核'];
				}
				break;
			case 4:
				//验证是否付费
				$courseMember = GuankeLivecoursemember::where('livecourse_id',$detail->id)->where('member_id',$this->getMid())->find();
				if($courseMember->isfavor != 1){
					return ['code'=>0,'error'=>'unfavor','msg'=>'您尚未付费'];
				}
				if($courseMember->isveryfy == 0){
					return ['code'=>0,'error'=>'unveryfy','msg'=>'请耐心等待审核'];
				}
			default:
				return  ['code'=>0,'msg'=>'无权操作'];
		}
		return ['code'=>1,'msg'=>'操作成功',data=>$detail->url];
	}
	
	public function enroll(){
		$this->initMember();
		//验证是否审核，审核通过
		$courseMember = GuankeLivecoursemember::where('livecourse_id',$this->request->param('id'))->where('member_id',$this->getMid())->find();
		if(empty($courseMember)){
			$data = [
					'livecourse_id' => $this->request->param('id'),
					'member_id'=>$this->getMid(),
					'cid'=>$this->getCid(),
					'isfavor'=>1,
					'islike'=>0,
			];
			GuankeLivecoursemember::create($data);
			$this->success('报名成功');
		}else{
			if($courseMember->isfavor == 1){
				if($courseMember->isveryfy == 1){
					$this->success('您已审核通过了');
				}else{
					$this->success('您已报过名了');
				}
			}else{
				$courseMember->isfavor = 1;
				$courseMember->save();
				$this->success('报名成功');
			}
		}
	}
}