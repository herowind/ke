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
use app\guanke\model\GuankeSchool;
use app\guanke\validate\SchoolValid;
use app\guanke\model\GuankeSlide;
use app\guanke\model\GuankeContentpage;
use app\guanke\model\GuankeCourse;
use app\guanke\model\GuankeTeacher;
use app\guanke\model\GuankeLivecourse;
use app\guanke\model\GuankeLiveschool;
use app\guanke\model\GuankeLiveactive;

class School extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}

	/**
	 * 公司旗下的所有学校
	 */
	public function index() {
		$pageData = GuankeSchool::manage()->keywords(['name',$this->request->param('keywords')])->order('sort desc')->paginate(10);
		$this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 编辑学校
	 */
	public function edit() {
	    $params = $this->request->param();
	    if($this->request->isPost()){
	        $validate = new SchoolValid();
	        $checkRst = $validate->check($params,'','edit');
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            
	            //上传logo
	            $rtnLogo = $this->uploadCut('logo');
	            if($rtnLogo !== false){
	                $params['logo'] = $rtnLogo;
	            }
	            //上传banner
	            $rtnBanner = $this->uploadCut('banner',640,320);
	            if($rtnBanner !== false){
	                $params['banner'] = $rtnBanner;
	            }	            
	            
	            if($params['id']){
	                //更新操作
	                $detail = GuankeSchool::manage()->find($params['id']);
	                $flag = $detail->save($params);
	                if($flag !== false){
	                    $this->success('保存成功',$this->getLastUrl());
	                }else{
	                    $this->error('保存失败');
	                }
	            }else{
	                //新增操作
	                $params['cid'] = $this->getCid();
	                $flag = GuankeSchool::create($params);
	                if($flag !== false){
	                    $this->success('新增成功',$this->getLastUrl());
	                }else{
	                    $this->error('新增失败');
	                }
	            }
	        }
	    }else{
	        if($params['id']){
	            $detail = GuankeSchool::manage()->find($params['id']);
	            $this->assign('detail',$detail);
	        }else{
	            $detail = [
	                'type' => 1,
	                'isdisplay'=>1,
	            ];
	            $this->assign('detail',$detail);
	        }
	         
	        return $this->fetch('edit');
	    }
	}
	
	/**
	 * 删除学校
	 */
	public function remove() {
	    $params = $this->request->param();
	    $flag = GuankeSchool::manage()->delete($params['id']);
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
	    $detail = GuankeSchool::manage()->find($id);
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
	    $detail = GuankeSchool::manage()->find($id);
	    
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
	
	/**
	 * 学校幻灯片
	 */
	public function slide(){
		$school_id = $this->request->param('school_id');
		$detail = GuankeSchool::manage()->find($school_id);
		if(empty($detail)){
			$this->error('请先完善学校基本信息','edit');
		}
		$pageData = GuankeSlide::manage()->where('channel','school')->where('channelid',$school_id)->order('sort desc')->paginate(10);
		$this->assign('pageData',$pageData);
		$this->assign('detail',$detail);
		return $this->fetch();
	}
	
	/**
	 * 学校幻灯片添加
	 */
	public function slideadd(){
        if ($this->request->isPost()) {
            $params = $this->request->param();
            if (empty($params['name'])) {
                $this->error('幻灯片名称不能为空');
            }
            
        	//上传banner
            $rtnBanner = $this->uploadCut('banner',640,320);
            if($rtnBanner !== false){
                $params['banner'] = $rtnBanner;
            }	
            
            $params['cid'] = $this->getCid();
            $params['channel'] = 'school';
            $params['channelid'] = $params['school_id'];
            $detail = GuankeSlide::create($params);
            if($detail && $detail->id>0){
            	$this->success('添加成功');
            }else{
            	$this->error('添加失败');
            }

        } else {
        	$detail = ['school_id'=>$this->request->param('school_id')];
            $this->assign('detail', $detail);
            exit($this->fetch());
        }
	}
	/**
	 * 学校详情页
	 */
	public function contentedit(){
		$params = $this->request->param();
		$detail = GuankeSchool::manage()->field('id,contenttype,contenturl,contentpageid')->find($params['school_id']);
		if(empty($detail)){
			$this->error('请先完善学校基本信息','edit');
		}
		if($this->request->isPost()){
			if($params['contentpageid']){
				//查询详情
				$contentDetail = GuankeContentpage::get($params['contentpageid']);
				if($contentDetail){
					//更新详情
					$contentDetail->content =  $params['content'];
					$contentDetail->save();
					
					//更新学校
					$detail->contenttype = 2;
					$detail->contenturl = '';
					$detail->save();
					$this->success('保存成功','index');
				}else{
					//存在脏数据，需要管理员介入
					$this->error('保存异常，请联系管理员');
				}
			}else{
				//新增详情
				$data['cid'] = $this->getCid();
				$data['content'] = $params['content'];
				$contentDetail = GuankeContentpage::create($data);
				if($contentDetail && $contentDetail->id){
					//更新学校
					$detail->contenttype = $params['contenttype'];
					$detail->contenturl = $params['contenturl'];
					$detail->contentpageid = $contentDetail->id;
					$detail->save();
					$this->success('保存成功','index');
				}else{
					$this->error('保存异常，请联系管理员');
				}
			}
		}else{
			$detail['content'] = GuankeContentpage::where('id',$detail['contentpageid'])->value('content');
			$this->assign('detail',$detail);
			return $this->fetch();
		}
	}
	
	public function stat(){
		//判断学校是否建立
		$cid = $this->getCid();
		$school = GuankeSchool::manage()->field('id,name,banner')->find();
		$school['courses'] = GuankeCourse::manage()->count();
		$school['teachers'] = GuankeTeacher::manage()->count();
		$school['liveactives'] = GuankeLiveactive::manage()->count();
		$school['livecourses'] = GuankeLivecourse::manage()->count();
		$school['liveschools'] = GuankeLiveschool::manage()->count();
		return ['code'=>1,'msg'=>'查询成功','data'=>$school];
	}

	
}