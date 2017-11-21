<?php
namespace think;
require 'site_local.php';
require __DIR__ . '/../extend/EasyWechat/vendor/autoload.php';
require __DIR__ . '/../../framework/base.php';
Container::get('app')->run()->send();