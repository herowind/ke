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
}