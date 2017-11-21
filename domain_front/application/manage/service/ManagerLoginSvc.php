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
namespace app\manage\service;

use think\facade\App;
use com\utils\FncCommon;
use com\utils\FncCrypt;
use app\manage\model\User;

class ManagerLoginSvc
{

    public static function doLogin($username, $password)
    {
        if (empty($username) || empty($password)) {
            return [
                'code' => 0,
                'msg' => '账号或密码不能为空'
            ];
        }
        
        $detail = User::where('username', $username)->find();
        
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
        $detail = User::field('id,username,pid,avatar,mobile')->where('id', $id)->find();
        if (! $isSys) {
            // 更新登录信息
            $detail->last_login_time = time();
            $detail->last_login_ip = FncCommon::getClientIp();
            $detail->save();
        }
        
        session(SES_MANAGER_AUTH, $detail);
        session(SES_MANAGER_AUTH_SIGN, FncCrypt::dataAuthSign($detail));
        return true;
    }

    /**
     * 获得session
     */
    public static function getSession()
    {
        $user = session(SES_MANAGER_AUTH);
        return $user;
    }

    /**
     * 是否登录
     * 
     * @return number
     */
    public static function isLogin()
    {
        $user = session(SES_MANAGER_AUTH);
        if (empty($user)) {
            return 0;
        } else {
            return session(SES_MANAGER_AUTH_SIGN) == FncCrypt::dataAuthSign($user) ? $user->id : 0;
        }
    }

    public static function getCid()
    {
        $user = session(SES_MANAGER_AUTH);
        if (empty($user)) {
            return 0;
        } else {
            if ($user->pid > 0) {
                return $user->pid;
            } else {
                return $user->id;
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
        $loginUser = session(SES_MANAGER_AUTH);
        $menuArr = include App::getConfigPath() . 'manage/menu.php';
        return $menuArr;
    }
	
	/**
	 * 登出
	 */
	public static function doLogout(){
		session(null);
	}
}
