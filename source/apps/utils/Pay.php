<?php

namespace Mall\Utils;

use Mall\Utils\Pay\AlipayClass as Alipay,
     Mall\Utils\Pay\WechatClass as Wechat,
     Mall\Utils\Pay\MobiAlipayClass as MobiAlipay,
     Mall\Utils\Pay\MobiWechatClass as MobiWechat;

class Pay
{

    public static function getPayType($di, $type)
    {
        switch($type)
        {
            //支付宝支付
            case 'alipay':
                return new Alipay($di);
            //手机支付宝支付
            case 'mobiAlipay':
                return new MobiAlipay($di);
            //微信支付
            case 'wechat':
                return new Wechat($di);
            //手机微信支付
            case 'mobiWechat':
                return new MobiWechat($di);
        }
    }

}

