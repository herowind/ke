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
// | 管理者登录控制器
// +----------------------------------------------------------------------
namespace app\admin\service;

use think\facade\App;
use com\utils\FncCommon;
use com\utils\FncCrypt;
use app\admin\model\Admin;

class AdminLoginSvc
{

    public static function doLogin($username, $password)
    {
        if (empty($username) || empty($password)) {
            return [
                'code' => 0,
                'msg' => '账号或密码不能为空'
            ];
        }
        
        $detail = Admin::where('username', $username)->find();
        
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
    public static function setSession($id, $isSys = false)
    {
        $detail = Admin::field('id,username,pid,avatar,mobile')->where('id', $id)->find();
        if (! $isSys) {
            // 更新登录信息
            $detail->last_login_time = time();
            $detail->last_login_ip = FncCommon::getClientIp();
            $detail->save();
        }
        
        session(SES_ADMIN_AUTH, $detail);
        session(SES_ADMIN_AUTH_SIGN, FncCrypt::dataAuthSign($detail));
        return true;
    }

    /**
     * 获得session
     */
    public static function getSession()
    {
        $admin = session(SES_ADMIN_AUTH);
        return $admin;
    }

    /**
     * 是否登录
     * 
     * @return number
     */
    public static function isLogin()
    {
        $admin = session(SES_ADMIN_AUTH);
        if (empty($admin)) {
            return 0;
        } else {
            return session(SES_ADMIN_AUTH_SIGN) == FncCrypt::dataAuthSign($admin) ? $admin->id : 0;
        }
    }
    
    /**
     * 是否登录
     *
     * @return number
     */
    public static function getRole()
    {
    	$admin = session(SES_ADMIN_AUTH);
    	if (empty($admin)) {
    		return 0;
    	} else {
    		if($admin->id == 1){
    			return 'supperadmin';
    		}else{
    			if($admin->pid === 0){
    				return 'agent';
    			}else{
    				return 'staff';
    			}
    		}
    	}
    }

    public static function getAgentId()
    {
        $admin = session(SES_ADMIN_AUTH);
        if (empty($admin)) {
            return 0;
        } else {
            if ($admin->pid > 0) {
                return $admin->pid;
            } else {
                return $admin->id;
            }
        }
    }

    /**
     * 获得登陆用户菜单
     * 
     * @return array
     */
    public static function getMenu()
    {
        $loginUser = session(SES_ADMIN_AUTH);
        $role = self::getRole();
        $menuArr = include App::getConfigPath() . 'admin/menu.php';
        foreach ($menuArr as $key1=>$topMenu){
        	foreach($topMenu['child'] as $key2=>$leftMenu ){
        		if(in_array($role, $leftMenu['role'])){
        			$menuArr[$key1]['child'][$key2]['isshow'] = 1;
        		}else{
        			$menuArr[$key1]['child'][$key2]['isshow'] = 0;
        		}
        	}
        }
        
        return $menuArr;
    }
	
	/**
	 * 登出
	 */
	public static function doLogout(){
		session(null);
	}
}
