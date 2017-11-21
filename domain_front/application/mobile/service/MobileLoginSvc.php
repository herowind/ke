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
// | 会员登录控制器
// +----------------------------------------------------------------------
namespace app\mobile\service;

use think\facade\App;
use app\manage\model\UserMember;

class MobileLoginSvc
{
    // 微信登录
    public static function doLoginwx($openid){
        $detail = UserMember::where('openid',$openid)->find();
        if(empty($detail)){
        	return [
        			'code' => -1,
        			'msg' => '账号信息不存在'
        	];
        }
        $isLogin = self::setSession($detail->id);
        if ($isLogin) {
        	return [
        			'code' => 1,
        			'msg' => '登陆成功',
        			'data' => $detail->id
        	];
        } else {
        	return [
        			'code' => 0,
        			'msg' => '系统异常，请稍后重试'
        	];
        }
    }
    
    // 账号登录
    public static function doLogin($username, $password)
    {
        if (empty($username) || empty($password)) {
            return [
                'code' => 0,
                'msg' => '账号或密码不能为空'
            ];
        }
        
        $detail = UserMember::where('username', $username)->find();
        
        // 验证密码
        if (empty($detail) || $detail->password != $password) {
            return [
                'code' => 0,
                'msg' => '账号或密码不正确'
            ];
        }
        // 验证状态
        if ($detail->status != 1) {
            return [
                'code' => 0,
                'msg' => '您的账号已被关闭，请联系管理员'
            ];
        }
        
        // session写入
        $isLogin = self::setSession($detail->id);
        if ($isLogin) {
            return [
                'code' => 1,
                'msg' => '登陆成功',
                'data' => $detail->id
            ];
        } else {
            return [
                'code' => 0,
                'msg' => '系统异常，请稍后重试'
            ];
        }
    }

    /**
     * 设置session
     * 
     * @param unknown $id            
     * @param string $isSys            
     * @return boolean
     */
    public static function setSession($id)
    {
        $detail = UserMember::field('id,cid,nickname,avatar,mobile')->where('id', $id)->find();       
        session('member'.$detail['cid'], $detail);
        //session('membersign'.$detail['cid'], FncCrypt::dataAuthSign($detail));
        return true;
    }

    /**
     * 获得session
     */
    public static function getSession($cid)
    {
        $detail = session('member'.$cid);
        return $detail;
    }

    /**
     * 是否登录
     * 
     * @return number
     */
    public static function isLogin($cid)
    {
        $detail = session('member'.$cid);
        if (empty($detail)) {
            return 0;
        } else {
            return $detail->id;
        }
    }
	
	/**
	 * 登出
	 */
	public static function doLogout(){
		session(null);
	}
}
