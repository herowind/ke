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
// | 模板消息管理
// +----------------------------------------------------------------------
namespace app\wechat\controller\manage;

use app\wechat\model\WechatSetting;
use app\wechat\model\WechatTemplate;
use app\wechat\model\WechatUsertemplatetask;

class Templatetask extends WechatController
{
	protected $officialAccount;
	protected $wechat;
    public function initialize()
    {
        parent::initialize();
        $this->wechat = WechatSetting::get($this->getCid());
        $this->officialAccount = $this->openPlatform->officialAccount($this->wechat->appid, $this->wechat->authorizer_refresh_token);
    }

    /**
     * 我的发送任务
     */
    public function index()
    {
		$pageData = WechatUsertemplatetask::manage()->paginate(10);
    	$this->assign('pageData',$pageData);
        return $this->fetch();
    }
    
    
    /**
     * 创建模板任务
     */
    public function edit(){
    	$params = $this->request->param();
    	if($this->request->isPost()){
    		//模板表单
    		$form = WechatTemplate::where('short_id',$params['short_id'])->value('form');
    		$form = json_decode($form,true);
    		foreach ($form as $key => $val){
    			if(isset($params['form_'.$val['k']])){
    				$val['v'] = $params['form_'.$val['k']];
    				$form[$key] = $val;
    			}
    		}
    		//保存的数据
    		$data['title'] = $params['title'];
    		$data['short_id'] = $params['short_id'];
    		$data['template_id'] = $params['template_id'];
    		$data['form'] = json_encode($form,JSON_UNESCAPED_UNICODE);
    		$data['totype'] = $params['totype']?:'group';
    		$data['touser'] = $params['touser']?:0;
    		$data['issend'] = 0;
    		$data['ispublish'] = 1;
    		$data['totime'] = $params['totime']?$params['totime']:time();
    		if($params['id']){
    			$detail = WechatUsertemplatetask::manage()->where('issend','0')->find($params['id']);
    			if($detail){
    				$data['id'] = $params['id'];
    				$detail->save($data);
    				$this->success('任务修改成功','index');
    			}else{
    				$this->error('任务不存在，或正在执行中无法修改');
    			}
    		}else{ 
    			$data['cid'] = $this->getCid();
    			WechatUsertemplatetask::create($data);
    			$this->success('操作成功','index');
    		}
    	}else{
    		if($params['id']){
    			$detail = WechatUsertemplatetask::manage()->find($params['id']);
    		}else{
    			
    			$template = WechatTemplate::field('title,form')->where('short_id',$params['short_id'])->find();
    			$detail['appid'] = $this->wechat->appid;
    			$detail['authorizer_refresh_token'] = $this->wechat->authorizer_refresh_token;
    			$detail['template_id'] = $params['template_id'];
    			$detail['short_id'] = $params['short_id'];
    			$detail['title'] = $template['title'];
    			$detail['form'] = $template['form'];
    		}
    		$this->assign('detail',$detail);
    		return $this->fetch();
    	}
    }
    
    public function copy(){
    	$params = $this->request->param();
    	if($params['id']){
    		$detail = WechatUsertemplatetask::manage()->find($params['id']);
    		$detail['id'] = '';
    	}else{
    		$this->error('复制错误');
    	}
    	$this->assign('detail',$detail);
    	return $this->fetch('edit');
    }

    /**
     * 删除
     */
    public function remove() {
    	$params = $this->request->param();
    	$flag = WechatUsertemplatetask::manage()->delete($params['id'],true);
    	if($flag==1){
    		return $this->success('删除成功');
    	}else{
    		return $this->error('信息不存在');
    	} 
    }

}