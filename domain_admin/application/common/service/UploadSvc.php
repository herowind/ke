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
// | 上传图片
// +----------------------------------------------------------------------
namespace app\common\service;

use think\image\Image;

class UploadSvc
{

    /**
     *
     * @param unknown $file            
     * @param string $subDir            
     * @param string $onlyThumb            
     * @return number[]|string[]|number[]|NULL[]
     */
    public static function uploadImage($file, $subDir = 'common', $onlyThumb = false)
    {
        // 移动到框架应用根目录/uploads/ 目录下
        $uploadPath = APP_UPLOAD_PATH . '/' . $subDir . '/';
        $uploadUrl = APP_UPLOAD_SITE . '/' . $subDir . '/';
        $info = $file->move($uploadPath);
        if ($info) {

            // 上传原图
            $image = Image::open($uploadPath . $info->getSaveName());
            $url = $uploadUrl . $info->getSaveName();
            
            // 生成缩略图
            if ($onlyThumb) {
                // 覆盖原图
                $thumbName = $info->getSaveName();
            } else {
                $thumbName = substr_replace($info->getSaveName(), '_thumb', strripos($info->getSaveName(), '.'), 0);
            }
            $image->thumb(200, 200, 3)->save($uploadPath . $thumbName);
            $thumbUrl = $uploadUrl . $thumbName;
            
            return [
                'code' => 1,
                'msg' => '上传成功',
                'url' => [
                    'o' => $url,
                    't' => $thumbUrl
                ]
            ];
        } else {
            // 上传失败获取错误信息
            return [
                'code' => 0,
                'msg' => $file->getError()
            ];
        }
    }
    
    /**
     *
     * @param unknown $file
     * @param string $subDir
     * @param string $onlyThumb
     * @return number[]|string[]|number[]|NULL[]
     */
    public static function uploadImageCut($file, $subDir = 'common', $width,$height)
    {
        // 移动到框架应用根目录/uploads/ 目录下
        $uploadPath = APP_UPLOAD_PATH . '/' . $subDir . '/';
        $uploadUrl = APP_UPLOAD_SITE . '/' . $subDir . '/';
        $info = $file->move($uploadPath);
        if ($info) {
            // 上传原图
            $image = Image::open($uploadPath . $info->getSaveName());
            $thumbName = $info->getSaveName();
            $image->thumb($width, $height, 3)->save($uploadPath . $thumbName);
            $thumbUrl = $uploadUrl . $thumbName;
            return [
                'code' => 1,
                'msg' => '上传成功',
                'url' => [
                    'o' => $thumbUrl,
                    't' => $thumbUrl
                ]
            ];
        } else {
            // 上传失败获取错误信息
            return [
                'code' => 0,
                'msg' => $file->getError()
            ];
        }
    }
}
