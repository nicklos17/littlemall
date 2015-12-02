<?php
/**
 * 配置需要身份验证的方法
 *
 * return array(
 *     controller => actions,   // array  actions,  若action为空数组，则表示所有action
 * );
 */
return array(
    'index' => array('synlogout'),  // index/synlogout  必须存在，勿删
    'order' => array(),
    'cart' => array('index', 'del', 'incr', 'decr', 'batchdel', 'expired', 'add', 'data'),
    'address' => array(),
    'support' => array(),
    // 'pay' => array(),
    'coupons' => array(),
    'code' => array(),
    'common' => array()
);
