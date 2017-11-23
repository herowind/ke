<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究  )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 客户首页
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\ZhiboCamera;

class Usercamera extends AdminController{
	
	public function initialize(){
		parent::initialize();
		$this->checkValidCid($this->request->param('cid'));
	}

	/**
	 * 摄像头列表页
	 */
	public function index() {
		$pageData = ZhiboCamera::where('cid',$this->request->param('cid'))->paginate(20);
		$this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 添加摄像头
	 */
	public function edit(){
		$params = $this->request->param();
		if($this->request->isPost()){
			
			if(empty($params['name'])){
				$this->error('摄像头名称不能为空');
			}
			if(empty($params['code'])){
				$this->error('摄像头代码不能为空');
			}
			if(empty($params['url'])){
				$this->error('摄像头链接不能为空');
			}
			
			if($params['id']){
				$detail = ZhiboCamera::where('cid',$params['cid'])->where('id',$params['id'])->find();
				$detail->name = $params['name'];
				$detail->code = $params['code'];
				$detail->url = $params['url'];
				$detail->remark = $params['remark'];
				$flag = $detail->save();
				if($flag !== false){
					$this->success('摄像头增加成功');
				}
			}else{
				$isExist = ZhiboCamera::where('code',$params['code'])->whereOr('url',$params['url'])->find();
				if($isExist){
					$this->error('该摄像头已经设置过，请重新输入');
				}
				$detail = ZhiboCamera::create($params);
				if($detail && $detail->id){
					$this->success('摄像头增加成功');
				}
			}
			$this->error('操作失败');
		}else{
			if($params['id']){
				$detail = ZhiboCamera::where('cid',$params['cid'])->where('id',$params['id'])->find();
			}else{
				$detail['cid'] = $params['cid'];
			}
			$this->assign('detail',$detail);
			exit($this->fetch());
		}
	}
	
	/**
	 * 预览直播
	 */
	public function preview(){
		$params = $this->request->param();
		$camera = ZhiboCamera::where('cid',$params['cid'])->where('id',$params['id'])->find();
		$camera->title = '直播预览';
		$this->assign('camera',$camera);
		exit($this->fetch());
	}
	
	/**
	 * 删除摄像头
	 */
	public function remove()
	{
		$params = $this->request->param();
		$detail = ZhiboCamera::where('cid',$params['cid'])->where('id',$params['id'])->find();
		if($detail){
			$flag = $detail->delete();
			if ($flag == 1) {
				return $this->success('删除成功');
			}
		}
		return $this->error('摄像头不存在');
		
	}
}