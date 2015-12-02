<?php
include __DIR__.'/baseRules.php';
//此处数据可以不用过滤 alipay发送json wechat soap
$rules['alinotify'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('out_trade_no', 'trade_no', 'trade_status'),
        )
);
return $rules;