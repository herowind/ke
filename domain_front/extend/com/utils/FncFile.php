<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace com\utils;

/**
 * 通用文件函数库
 * @author oliver
 *
 */
class FncFile{
	/**
	 * 采集远程文件
	 * @access public
	 * @param string $remote 远程文件名
	 * @param string $local 本地保存文件名
	 * @return mixed
	 */
	public static function curlDownload($remote,$local) {
		$cp = curl_init($remote);
		$fp = fopen($local,"w");
		curl_setopt($cp, CURLOPT_FILE, $fp);
		curl_setopt($cp, CURLOPT_HEADER, 0);
		curl_exec($cp);
		curl_close($cp);
		fclose($fp);
	}
	/**
	 * 远程文件下载
	 * @param unknown $remote
	 * @param unknown $local
	 */
	public static function fileDownload($remote,$local){
		$content = file_get_contents($remote);
		$flag = self::checkPath(dirname($local));
		if($flag){
			file_put_contents($local, $content);	
		}
		return $flag;
	}
	
	/**
	 * 检查目录是否可写
	 * @param  string   $path    目录
	 * @return boolean
	 */
	public static function checkPath($path)
	{
		if (is_dir($path)) {
			return true;
		}
		if (mkdir($path, 0755, true)) {
			return true;
		} else {
			return false;
		}
	}
}