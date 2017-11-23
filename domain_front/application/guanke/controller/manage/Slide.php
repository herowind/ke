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
// | 学校管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeSlide;

class Slide extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}
	
	/**
	 * 删除幻灯片
	 */
	public function remove() {
	    $params = $this->request->param();
	    $flag = GuankeSlide::manage()->delete($params['id']);
	    if($flag==1){
	        return $this->success('删除成功');
	    }else{
	        return $this->error('信息不存在');
	    }
	}
	
	/**
	 * 排序变更
	 */
	public function sortChange()
	{
	    $id = $this->request->param('id');
	    $sort = $this->request->param('sort');
	    $detail = GuankeSlide::manage()->find($id);
	    $detail->sort = $sort;
	    $flag = $detail->save();
	    if ($flag !== false) {
	        $this->success('操作成功', '', $sort);
	    } else {
	        $this->error('操作失败', '', $sort);
	    }
	}	
	
	/**
	 * 状态变更
	 */
	public function statusChange(){
	    $id = $this->request->param('id');
	    $field = $this->request->param('field');
	    $detail = GuankeSlide::manage()->find($id);
	    
	    if($detail->$field === 1){
	        $detail->$field = 0;
	    }else{
	        $detail->$field = 1;
	    }
	    $flag = $detail->save();
	    if($flag !== false){
	        $this->success('操作成功','',$detail->$field);
	    }else{
	        $this->error('操作失败','',$detail->$field);
	    }
	}
	
}