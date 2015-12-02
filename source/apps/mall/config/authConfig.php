<?php
/**
 * 配置需要身份验证的方法
 *
 * return array(
 *     controller => actions,   // array  actions,  若action为空数组，则表示所有action
 * );
 */
return array(
    'index' => array('synlogout', 'logout'),  // index/synlogout  必须存在，勿删
    'order' => array('index', 'detail', 'confirm', 'buyNow', 'buyCart', 'getShipPay', 'Payments', 'go', 'getExpress', 'confirmOrder'),
    'cart' => array('index', 'del', 'incr', 'decr', 'batchdel', 'expired', 'add', 'data'),
    'address' => array(),
    'support' => array('index', 'apply', 'addSupport', 'uploadImg', 'delImg', 'express', 'submitExpress', 'progress'),
   // 'pay' => array(),
    'coupons' => array(),
    'code' => array(),
    'common' => array('bridge')
);
