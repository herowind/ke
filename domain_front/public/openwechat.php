<?php
define('APP_SITE', 'http://ke.qyhzlm.com');
include __DIR__ . '/../extend/EasyWechat/vendor/autoload.php';
use EasyWeChat\OpenPlatform\Server\Guard;
use EasyWeChat\Factory;
//use EasyWeChat\OpenPlatform\Auth\VerifyTicket;
$wechatOptions = include __DIR__.'/../config/wechat.php';

$openPlatform = Factory::openPlatform($wechatOptions['component']);
$server = $openPlatform->server;

// 处理授权成功事件
$server->push(function ($message) use ($openPlatform){
	$openPlatform['logger']->debug('授权成功:',['msg'=>$message]);
}, Guard::EVENT_AUTHORIZED);

// 处理授权更新事件
$server->push(function ($message) use ($openPlatform){
   $openPlatform['logger']->debug('授权更新:',['msg'=>$message]);
}, Guard::EVENT_UPDATE_AUTHORIZED);
 
// 处理授权取消事件
$server->push(function ($message) use ($openPlatform){
	$openPlatform['logger']->debug('授权取消:',['msg'=>$message]);
   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_URL, APP_SITE."/wechat/manage.openplatform/remove.html?appid=".$message['AuthorizerAppid']); 
   $output = curl_exec($ch); 
   curl_close($ch); 
}, Guard::EVENT_UNAUTHORIZED);

//$verifyTicket = new VerifyTicket($wechatOptions['component']['app_id']);
//$verify_ticket =$verifyTicket->getTicket();
//$openPlatform['logger']->debug('旧的ticket:',['ticket'=>$verify_ticket]);

return $server->serve();


