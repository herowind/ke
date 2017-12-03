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
// | 发送模板通知
// +----------------------------------------------------------------------
namespace app\cron;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\wechat\service\TemplateSvc;

class Cronsendtemplatemessage extends Command
{
	protected function configure()
	{
		$this->setName('Cronsendtemplatemessage')->setDescription('send template message ');
	}
	protected function execute(Input $input, Output $output)
	{
		$output->writeln("Start...".date('Y-m-d H:i:s'));
		TemplateSvc::execTask();
		$output->writeln("Finish...".date('Y-m-d H:i:s'));
	}
}