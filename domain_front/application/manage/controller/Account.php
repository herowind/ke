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
// | 管理者账号
// +----------------------------------------------------------------------
namespace app\manage\controller;

use app\common\service\UploadSvc;
use app\manage\model\User;

class Account extends ManageController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 编辑用户
     */
    public function edit()
    {
        $user = User::field('id,username,realname,mobile,avatar')->find($this->getUid());
        if ($this->request->isPost()) {
            $params = $this->request->param();
            if (empty($params['realname'])) {
                $this->error('用户姓名不能为空');
            }
            if (empty($params['mobile'])) {
                $this->error('手机号码不能为空');
            }
            
            // 上传头像
            if ($params['check_avatar'] != $params['oldcheck_avatar']) {
                $file = $this->request->file('file_avatar');
                $rtnDataUpload = UploadSvc::uploadImage($file, 'manage',true);
                $user->avatar = $rtnDataUpload['url']['t'];
            }
            
            $user->realname = $params['realname'];
            $user->mobile = $params['mobile'];
            $flag = $user->save();
            if ($flag !== false) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->assign('detail', $user);
            exit($this->fetch());
        }
    }

    /**
     * 修改密码
     */
    public function password()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            if (empty($params['new_pass']) || empty($params['confirm_pass']) || empty($params['old_pass'])) {
                $this->error('密码不能为空');
            }
            
            if ($params['new_pass'] != $params['confirm_pass']) {
                $this->error('新密码和确认密码不一致');
            }
            
            if (strlen($params['new_pass']) < 6) {
                $this->error('密码长度不能少于6位');
            }
            
            $user = User::field('id,password')->find($this->getUid());
            if ($user->password == $params['old_pass']) {
                $user->password = $params['new_pass'];
                $user->save();
                $this->success('密码修改成功', config('manage.website.logout'));
            } else {
                $this->error('旧密码不正确');
            }
        } else {
            exit($this->fetch());
        }
    }
}