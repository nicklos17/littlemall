<?php
$admHost = 'my.yunduo.com';
$admPort = 8081;
$admUrl = $admHost . ':' . $admPort;

return array(
    'domain' => 'http://mall.yunduo.com:8081',
    'siteid' => 3,
    'host' => $admHost,
    'port' => $admPort,
    'loginUrl' => 'http://' . $admUrl . '/login', // 登录接口
    'logoutUrl' => 'http://' . $admUrl . '/index/logout',   // 退出接口
    'logUrl' => 'http://' . $admUrl . '/index/ajaxLog',     // 日志记录接口
    'encKey' => '@#a33dff35145',

    'redis' => array('host' => '127.0.0.1', 'port' => '6379', 'timeout' => '2.5', 'dbname' => 13)
);