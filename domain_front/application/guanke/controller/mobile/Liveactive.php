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
// | 直播活动页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\zhibo\model\ZhiboCamera;
use app\guanke\model\GuankeLiveactive;
use app\guanke\model\GuankeLivemember;
use app\guanke\model\GuankeContentpage;

class Liveactive extends LiveController {
	public function initialize() {
		parent::initialize ();
		$this->livetype = 'liveactive';
	}
	
	/**
	 * 直播活动列表页
	 */
	public function index(){
		$list = GuankeLiveactive::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch (); 
	}
	
	/**
	 * 直播活动详细页
	 */
	public function detail(){
		$this->initMember();
		$live_id = $this->request->param('live_id');
		$detail = GuankeLiveactive::find($live_id);
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 直播活动列表数据
	 */
	public function listdata(){
		$list = GuankeLiveactive::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$list->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	public function detaildata(){
		$live_id = $this->request->param('live_id');
		$detail = GuankeLiveactive::find($live_id);
		$detail->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 获得详细
	 */
	public function content(){
		$contentpageid = GuankeLiveactive::where('id',$this->request->param('live_id'))->value('contentpageid');
		if($contentpageid){
			return GuankeContentpage::where('id',$contentpageid)->value('content');
		}else{
			return '';
		}
	}

	/**
	 * 验证时间
	 */
	protected function checktime($live){
		switch ($live['process']['status']){
			case 'ready':
				return ['code'=>0,'msg'=>'直播尚未开始，请耐心等待','error'=>'untime'];
			case 'start':
				return ['code'=>1,'msg'=>'验证通过'];
			case 'finish':
				return ['code'=>0,'msg'=>'直播已结束','error'=>'untime'];
		}
		return ['code'=>0,'msg'=>'直播状态异常','error'=>'untime'];
	
	}
}