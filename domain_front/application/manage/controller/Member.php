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
// | 管理首页
// +----------------------------------------------------------------------
namespace app\manage\controller;

use app\manage\model\UserTrade;
use think\Db;

class Member extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}

	/**
	 * 首页面
	 */
	public function index() {
		$menu = $this->getMenu();
		$this->assign('menu',$menu);
		return $this->fetch();
	}
	
	/**
	 * 消费记录
	 */
	public function consumelist(){
		$params = $this->request->param();
		// 关键词
		$where[] = ['goodstype','in',['livecourse','liveschool','liveactive']];
		if (! empty($params['keywords'])) {
			$where[] = ['nickname','like','%'.$params['keywords'].'%'];
		}
		$pageData = Db::view('UserTrade', 'id,cid,member_id,goodstype,goodsinfo,type,price,paytype,ispay,create_time')
		->view('UserMember', 'nickname,mobile,openid', 'UserTrade.member_id=UserMember.id and UserTrade.cid='.$this->getCid().' and UserTrade.type=1 and ispay	= 1')
		->where($where)
		->paginate(20)->each(function($item, $key){
			$goodstypes = config('manage.zhiboprice.type');
		    $item['goodsinfo'] = json_decode($item['goodsinfo'],true);
		    $item['goodstypetext'] = $goodstypes['livecourse'];
		    $item['create_time'] = date('Y-m-d H:i',$item['create_time']);
		    return $item;
		});
		
		$this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
}