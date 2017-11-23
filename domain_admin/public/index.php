<?php
namespace think;
require 'site_prod.php';
require __DIR__ . '/../../framework/base.php';
Container::get('app')->run()->send();