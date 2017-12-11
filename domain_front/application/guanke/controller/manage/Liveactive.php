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
// | 活动直播管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\manage\controller\ManageController;
use app\guanke\model\GuankeLiveactive;
use app\guanke\service\GuankeManageSvc;
use app\guanke\validate\LiveactiveValid;
use think\Db;
use app\guanke\model\GuankeContentpage;
use app\guanke\model\GuankeSchool;

class Liveactive extends ManageController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 所有直播活动
     */
    public function index()
    {
        $params = $this->request->param();
        // 摄像头
        if (! empty($params['camera_id'])) {
            $where[] = ['camera_id','=',$params['camera_id']];
        }
                
        $pageData = GuankeLiveactive::manage()->keywords([
            'name',
            $this->request->param('keywords')
        ])
            ->where($where)
            ->order('sort desc')
            ->paginate(10);

        $school = GuankeSchool::manage()->where('type',1)->find();
        $this->assign('school', $school);
        $this->assign('pageData', $pageData);
        return $this->fetch();
    }

    /**
     * 编辑活动
     */
    public function edit()
    {
        $params = $this->request->param();
        if ($this->request->isPost()) {
            $validate = new LiveactiveValid();
            $checkRst = $validate->check($params, '', 'edit');
            if ($checkRst !== true) {
                // 验证失败
                $this->error($validate->getError());
            } else {
                // 验证通过
                if($params['camera_id']){
                    $params['camera'] = GuankeManageSvc::getCameraJson($params['camera_id']);
                }
                
                //上传banner
                $rtnBanner = $this->uploadCut('banner',640,320);
                if($rtnBanner !== false){
                	$params['banner'] = $rtnBanner;
                }
                
                if ($params['id']) {
                    // 更新操作
                    $detail = GuankeLiveactive::manage()->find($params['id']);
                    $flag = $detail->save($params);
                    if ($flag !== false) {
                        $this->success('保存成功', $this->getLastUrl());
                    } else {
                        $this->error('保存失败');
                    }
                } else {
                    // 新增操作
                    $params['cid'] = $this->getCid();
                    $flag = GuankeLiveactive::create($params);
                    if ($flag !== false) {
                        $this->success('新增成功', $this->getLastUrl());
                    } else {
                        $this->error('新增失败');
                    }
                }
            }
        } else {
            if ($params['id']) {
                $detail = GuankeLiveactive::manage()->find($params['id']);
                $this->assign('detail', $detail);
            } else {
                $detail = [
                    'isdisplay' => 1
                ];
                $this->assign('detail', $detail);
            }
            
            return $this->fetch('edit');
        }
    }

    /**
     * 删除课程
     */
    public function remove()
    {
        $params = $this->request->param();
        $flag = GuankeLiveactive::manage()->delete($params['id']);
        if ($flag == 1) {
            return $this->success('删除成功');
        } else {
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
        $detail = GuankeLiveactive::manage()->find($id);
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
    public function statusChange()
    {
        $id = $this->request->param('id');
        $field = $this->request->param('field');
        $detail = GuankeLiveactive::manage()->find($id);
        
        if ($detail->$field === 1) {
            $detail->$field = 0;
        } else {
            $detail->$field = 1;
        }
        $flag = $detail->save();
        if ($flag !== false) {
            $this->success('操作成功', '', $detail->$field);
	    }else{
	        $this->error('操作失败','',$detail->$field);
	    }
	}
	

	public function favormembers(){
		$id = $this->request->param('live_id');
		if(empty($id)){
			$this->error('请先完善直播基本信息','edit');
		}
		$pageData = Db::view('GuankeLivemember', 'id,livetype,live_id,member_id,isfavor,isveryfy,create_time')->view('UserMember', 'mobile,openid,nickname,avatar,province,city', "GuankeLivemember.member_id = UserMember.id and GuankeLivemember.livetype='liveactive' and GuankeLivemember.live_id ={$id}")->paginate(20);
		$detail = GuankeLiveactive::manage()->find($id);
		$this->assign('pageData',$pageData);
		$this->assign('detail',$detail);
		return $this->fetch();
	}
	
	/**
	 * 状态变更
	 */
	public function memberStatusChange()
	{
		$id = $this->request->param('id');
		$field = $this->request->param('field');
		$detail = GuankeLiveactive::manage()->where('id',$id)->find();
	
		if ($detail->$field === 1) {
			$detail->$field = 0;
		} else {
			$detail->$field = 1;
		}
		$flag = $detail->save();
		if ($flag !== false) {
			$this->success('操作成功', '', $detail->$field);
		}else{
			$this->error('操作失败','',$detail->$field);
		}
	}

	/**
	 * 活动详情页
	 */
	public function contentedit(){
		$params = $this->request->param();
		$detail = GuankeLiveactive::manage()->field('id,contenttype,contenturl,contentpageid')->find($params['live_id']);
		if(empty($detail)){
			$this->error('请先完善直播基本信息','edit');
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
					$detail->contenttype = $params['contenttype'];
					$detail->contenturl = $params['contenturl'];
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
	
}