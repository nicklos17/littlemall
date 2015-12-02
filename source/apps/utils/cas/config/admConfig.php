<?php
$admHost = 'adm.yunduo.com';
$admPort = 8081;
$admUrl = $admHost . ':' . $admPort;

return array(
    'domain' => 'http://admmall.yunduo.com:8081',
    'siteid' => 3,
    'host' => $admHost,
    'port' => $admPort,
    'loginUrl' => 'http://' . $admUrl . '/index/login', // 登录接口
    'logoutUrl' => 'http://' . $admUrl . '/index/logout',   // 退出接口
    'logUrl' => 'http://' . $admUrl . '/index/ajaxLog',     // 日志记录接口
    'encKey' => '@#a33dff35145',

    'redis' => array('host' => '192.168.0.20', 'port' => '49156', 'timeout' => '2.5', 'dbname' => 13)
);
