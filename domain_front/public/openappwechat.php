<?php
define('APP_SITE', 'http://ke.qyhzlm.com');
include __DIR__ . '/../extend/EasyWechat/vendor/autoload.php';
use EasyWeChat\Factory;
//use EasyWeChat\OpenPlatform\Auth\VerifyTicket;
$wechatOptions = include __DIR__.'/../config/wechat.php';

$openPlatform = Factory::openPlatform($wechatOptions['component']);
$server = $openPlatform->server;

$server->push(function ($message) {
	switch ($message['MsgType']) {
		case 'event':
			return '收到事件消息';
			break;
		case 'text':
			return '收到文字消息';
			break;
		case 'image':
			return '收到图片消息';
			break;
		case 'voice':
			return '收到语音消息';
			break;
		case 'video':
			return '收到视频消息';
			break;
		case 'location':
			return '收到坐标消息';
			break;
		case 'link':
			return '收到链接消息';
			break;
			// ... 其它消息
		default:
			return '收到其它消息';
			break;
	}
});

