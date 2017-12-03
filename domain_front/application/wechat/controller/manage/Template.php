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
use app\wechat\model\WechatUsertemplate;
use app\wechat\model\WechatTemplate;
use think\Db;
use app\wechat\model\WechatUsertemplatetask;

class Template extends WechatController
{
	protected $officialAccount;
    public function initialize()
    {
        parent::initialize();
        $wechat = WechatSetting::get($this->getCid());
        $this->officialAccount = $this->openPlatform->officialAccount($wechat->appid, $wechat->authorizer_refresh_token);
    }

    /**
     * 我的模板
     */
    public function index()
    {
    	$data = $this->officialAccount->template_message->getPrivateTemplates();
    	$this->assign('data',$data);
        return $this->fetch();
    }
    
    /**
     * 添加模板
     */
    public function add()
    {
    	if($this->request->isPost()){
    		$data = $this->officialAccount->template_message->addTemplate($this->request->param('short_id'));
    		if($data['errmsg'] == 'ok'){
    			$this->success('添加成功');
    		}else{
    			$this->error("添加失败【{$data['errmsg']}】");
    		}
    	}else{
    		exit($this->fetch()) ;
    	}
    	
    }
    
    /**
     * 删除模板
     */
    public function remove()
    {
    	$data = $this->officialAccount->template_message->deletePrivateTemplate($this->request->param('template_id'));
    	if($data['errmsg'] == 'ok'){
    		$detail = WechatUsertemplate::get($this->request->param('template_id'));
    		$detail->delete(true);
    		$this->success('删除成功');
    	}else{
    		$this->error("删除失败【{$data['errmsg']}】");
    	}
    }
    
    /**
     * 用户模板
     */
    public function usertemplate(){
    	$list = WechatUsertemplate::manage()->select();
    	$this->assign('list',$list);
    	return $this->fetch();
    }
    
    public function usertemplateadd(){
    	//需要添加的模板
    	$list = Db::view('WechatTemplate', 'short_id,title,primary_industry,deputy_industry,content,example')
		    ->view('WechatUsertemplate', 'template_id,cid', 'WechatTemplate.short_id=WechatUsertemplate.short_id and WechatUsertemplate.cid='.$this->getCid(), 'LEFT')
		    ->where('template_id', 'null')
		    ->select();
    	$error = '';
		foreach ($list as $val){
			$rtnData = $this->officialAccount->template_message->addTemplate($val['short_id']);
			if($rtnData['errmsg'] == 'ok'){
				$data['template_id'] = $rtnData['template_id'];
				$data['short_id'] = $val['short_id'];
				$data['title'] = $val['title'];
				$data['cid'] = $this->getCid();
				WechatUsertemplate::create($data);
			}else{
				$error = $error."{$val['title']}模板添加失败，";
			}
		}
		if($error){
			$this->error($error,'usertemplate');
		}else{
			$this->success('添加成功','usertemplate');
		}
    }
    
    public function usertemplateremove(){
    	$data = $this->officialAccount->template_message->deletePrivateTemplate($this->request->param('template_id'));
    	if($data['errmsg'] == 'ok'){
    		$detail = WechatUsertemplate::get($this->request->param('template_id'));
    		$detail->delete(true);
    		$this->success('删除成功');
    	}else{
    		$this->error("删除失败【{$data['errmsg']}】");
    	}
    }
    
    public function usertemplatechoose(){
    	//需要添加的模板
    	$addlist = Db::view('WechatTemplate', 'short_id,title,primary_industry,deputy_industry,content,example')
    	->view('WechatUsertemplate', 'template_id,cid', 'WechatTemplate.short_id=WechatUsertemplate.short_id and WechatUsertemplate.cid='.$this->getCid(), 'LEFT')
    	->where('template_id', 'null')
    	->select();
    	foreach ($addlist as $val){
    		$rtnData = $this->officialAccount->template_message->addTemplate($val['short_id']);
    		if($rtnData['errmsg'] == 'ok'){
    			$data['template_id'] = $rtnData['template_id'];
    			$data['short_id'] = $val['short_id'];
    			$data['title'] = $val['title'];
    			$data['cid'] = $this->getCid();
    			WechatUsertemplate::create($data);
    		}
    	}
    	$list = WechatUsertemplate::manage()->select();
    	$this->assign('list',$list);
    	exit($this->fetch());
    	
    }
   
}