<?php
    return array(
        'domain'=>'http://test.mall.yunduo.com',
        //thrift服务
        'thrift' => array('ip' =>'127.0.0.1','port' => '6013'),
        //redis配置
        'redis' => array('server' => '127.0.0.1', 'port' => '6379', 'timeout' => '2.5'),

        //+++++++++++++短信文案++++++++++++++++
        //注册
        'regMsg' => '云朵儿童安全鞋：验证码：%s。感谢您注册成为云朵会员，该验证码30分钟内有效。',
        //忘记密码
        'resetMsg' => '云朵儿童安全鞋：验证码：%s。您正在使用找回密码功能，如果不是您本人操作，请忽略，该验证码30分钟内有效。',
        //修改密码
        'changeMsg' => '云朵儿童安全鞋：验证码：%s。您正在使用修改密码功能，如果不是您本人操作，请忽略，该验证码30分钟内有效。',
        //更改绑定邮箱
        'emailMsg' => '云朵儿童安全鞋：验证码：%s。您正在使用更改邮箱功能，如果不是您本人操作，请忽略，该验证码30分钟内有效。',
        //发邮件配置 $this->user-> $this->user->
        'emailFrom' => 'noreply@yunduo.com',
        'emailName' => 'noreply@yunduo.com',
        'mail_subject' => '云朵安全验证',
        'emailConf' => array (
                'protocol'  => 'smtp',
                'smtp_host' => 'smtp.exmail.qq.com',
                'smtp_user' => 'noreply@yunduo.com',
                'smtp_pass' => 'y123456unduo',
                'smtp_port' => 465,
                'charset'   => 'utf-8',
                'wordwrap'  => FALSE,
                'mailtype'  => 'html',
                'smtp_crypto'   => 'ssl',
                'newline'   => "\r\n"
        ),
        //商品列表上传默认路径
        'goodsImg'=>'public/images/goods',
        //访问路径
        'goodsImgAccess'=>'images/goods',
        //商品缩略图上传默认路径
        'goodsThumb'=>'public/images/goods_thumb',
        //访问路径
        'goodsThumbAccess'=>'images/goods_thumb',

        //商品属性图上传默认路径
        'attrsImg'=>'public/images/attrs',
        //访问路径
        'attrsImgAccess'=>'images/attrs',

        //编辑器上传图片存储路径
        'umeditor'=>'public/images/umeditor',
        //同个用户两次获得验证码的间隔
        'haveCap' => '60',
        //验证码时效:半小时
        'capValid' => '1800',
        //邮箱验证url
        'emailValidurl' => 'http://my.yunduo.com/mailverify/validemail',

        //邮箱验证过期时间
        'emailinvalid' => '86400',

        //静态资源服务器地址
        'staticServer' => '/',
        //pc版主页
        'siteUrl' => 'http://test.www.yunduo.com/',
        //手机版主页
        'mobileUrl' => 'http://test.m.yunduo.com/',
        //商城主页
        'mallUrl' => 'http://test.mall.yunduo.com/',
        //后台管理
        'admPath' => 'http://mdaall.yunduo.com:8081',
        //用户中心主页
        'ucenterPath' => 'http://test.my.yunduo.com',

        //输出错误页面的信息
        'flagMsg' => array(
            '10000' => '服务器出现异常，请稍候重试',
            '10001' => '用户不存在',
            '10021' => '标签名已存在',
            '10022' => '商品属性名已存在',
            '10023' => '请先删除子属性',
            '10024' => '该分类下已存在此子分类',
            '10025' => '请先删除子分类',
            '10026' => '该地区快递费用已存在',
            '10027' => '订单号不存在',
            '10028' => '请检查所有订单是否符合更改条件',
            '10029' => '此款商品已经售罄,请更换属性',
            '10030' => '没找到相应的属性',
            '10031' => '该订单已发货',
            '10032' => '找不到对应商品',
            '10033' => '所选择的上级分类不能是当前分类',
            '10034' => '所选择的上级分类不能是当前分类的下级分类',
            '10035' => '所要删除的分类下有商品，请先删除所有商品后再试',
            '10036' => '不存在此商品',
            '10037' => '不存在此商品图片',
            '10038' => '商品名已存在',
            '10039' => '商品sn码已存在',
            '10040' => '不存在的商品属性',
            '10041' => '该云码格式错误',
            '10042' => '该云码已过期',
            '10043' => '该云码已禁用',
            '10044' => '该云码已使用',
            '10045' => '', //需要图片验证码
            '10046' => '验证码错误',
            '10047' => '验证码失效',
            '10048' => '该云码不存在',
            '10049' => '订单不存在或订单状态错误',
            '10050' => '该订单已超过7天退货期',
            '10051' => '该产品已超过30天的换货期',
            '10052' => '该产品已经超过售后服务期',
            '10053' => '售后申请类型错误',
            '10054' => '该产品售后申请数量超过允许的最大值',
            '10055' => '该售后单已经提交了快递单号，如有问题，请联系客服！',
            '10056' => '图片上传失败，文件过大（最大2M）或格式错误',
            '10057' => '售后单不存在或状态错误',
            '10057' => '售后单不存在或状态错误',
        ),
        //支付sdk配置
        'payment' =>
            array(
                'alipay' =>
                    array(
                        'partner' => '2088311599513065',
                        'key' => 'dh1ryftz705dl4mt75oq8y3eflhsp1wf',
                        'seller_id' => 'yunduo1@yunduo.com',
                        'input_charset' => strtolower('utf-8'),
                        'sign_type' => strtoupper('MD5'),
                        'transport' => 'http',

                        //ca证书路径地址，用于curl中ssl校验
                        'cacert' => __DIR__ . '/../apps/utils/pay/alipay/cacert.pem',
                         //支付完成后的回调处理页面额
                        'notify_url' => 'http://test.mall.yunduo.com/pay/alinotify',
                        'return_url' => 'http://test.mall.yunduo.com/order'
                    ),
                'wechat' =>
                    array(
                        'APPID' => 'wxe0725aa5b1fa7448',
                        //受理商ID，身份标识
                        'MCHID' => '1219205801',
                        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
                        'KEY' => '74160b347db8b90acc5e52cf5b3c3dee',
                        //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
                        'APPSECRET' => '4f3b4183567672a365653da3cd4444b6',
                        //=======【证书路径设置】=====================================
                        //证书路径,注意应该填写绝对路径
                        'SSLCERT_PATH' => __DIR__ . '/../apps/utils/Pay/wechat/lib/cacert/apiclient_cert.pem',
                        'SSLKEY_PATH' =>  __DIR__ . '/../apps/utils/Pay/wechat/lib/cacert/apiclient_key.pem',
                        //=======【异步通知url设置】===================================
                        //异步通知url，商户根据实际开发过程设定
                        'NOTIFY_URL' => 'http://test.mall.yunduo.com/pay/wxnotify',
                        //=======【curl超时设置】===================================
                        //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
                        'CURL_TIMEOUT' => 30
                    ),
                'mobiAlipay' =>
                    array(
                        'partner' => '2088311599513065',
                        'key' => 'dh1ryftz705dl4mt75oq8y3eflhsp1wf',
                        'seller_id' => 'yunduo1@yunduo.com',
                        'input_charset' => strtolower('utf-8'),
                        'private_key_path' => __DIR__ . '/../apps/utils/Pay/mobiAlipay/key/rsa_private_key.pem',
                        'ali_public_key_path' => __DIR__ . '/../apps/utils/Pay/mobiAlipay/key/alipay_public_key.pem',
                        'sign_type' => strtoupper('RSA'),
                        'transport' => 'http',

                        //ca证书路径地址，用于curl中ssl校验
                        'cacert' => __DIR__ . '/../apps/utils/Pay/mobiAlipay/cacert.pem',
                         //支付完成后的回调处理页面额
                        'notify_url' => 'http://test.m.mall.yunduo.com/pay/alinotify',
                        'return_url' => 'http://test.m.mall.yunduo.com/ng/#/order'
                    ),
                'mobiWechat' =>
                    array(
                        'mchid' => '1219205801',
                        'appid' => 'wxe0725aa5b1fa7448',
                        'key' => '74160b347db8b90acc5e52cf5b3c3dee',
                        'appsecret' => '4f3b4183567672a365653da3cd4444b6',
                        'sslcert_path' => __DIR__ . '/../apps/utils/pay/mobiWechat/cacert/apiclient_cert.pem',
                        'sslkey_path' => __DIR__ . '/../apps/utils/pay/mobiWechat/cacert/apiclient_key.pem',
                         'notify_url' => 'http://test.m.mall.yunduo.com/pay/wxnotify',
                        'curl_timeout' => '30',
                        'js_api_call_url' => 'http://test.m.mall.yunduo.com/pay/redirectPay'
                    )
            ),

        //售后服务失效：back:退货； exchange：换货； changeModel：更换智能模块
        'supportTime' =>
            array(
                'back' => 7*86400,
                'exchange' => 30*86400,
                'exchangeModel' => 183*86400
            ),

        'expressCode' =>
            array(
                'shunfeng' => '顺丰快递',
                'shentong' => '申通快递',
                'zhongtong' => '中通速递',
                'yunda' => '韵达快运',
                'debangwuliu' => '德邦物流',
                'yuantong' => '圆通速递',
                'EMS' => 'ems'
            ),
         'express' =>
            array(
                '1' => '申通快递',
                '3' => '圆通速递',
                '5' => '顺风速递',
                '7' => '顺风四日达',
                '9' => 'EMS'
            ),
        'supportProgress' =>
            array(
                //未审核
                '1' => '您提交的售后申请客服将会第一时间为您解决，请耐心等候。',
                //审核通过
                '3' => '您的售后申请已通过审核，请尽快邮寄童鞋到云朵售后服务部。',
                //审核未通过
                '5' => '亲爱的客户，很抱歉您的商品出现这样的问题。已与您电话沟通，您的审核未通过，已将您的售后申请取消，感谢您对云朵的支持。',
                //已解决
                '7' => '已解决',
                //已关闭
                '9' => '已关闭',
                //已收到用户寄来的鞋子，符合退货或换货条件
                '11' => array(
                    '1' => '亲爱的客户，我们已经收到您邮寄回来的鞋子，符合退货条件，您的退款我们将第一时间为您处理，请注意查收。',
                    '3' => '亲爱的客户，我们已经收到您邮寄回来的鞋子，符合换货条件，为您更换好的鞋子正在快马加鞭寄往您的府上，请注意查收。',
                    '5' =>  '亲爱的客户，我们已经收到您邮寄回来的智能模块，符合换货条件，为您更换好的模块正在快马加鞭寄往您的府上，请注意查收。'
                ),
                //已收到用户寄来的鞋子，但是不符合退货或换货条件
                '13' => array(
                    '1' => '亲爱的客户，我们已经收到您邮寄回来的鞋子，但是很抱歉，不符合退货条件。',
                    '3' => '亲爱的客户，我们已经收到您邮寄回来的鞋子，但是很抱歉，不符合换货条件。',
                    '5' =>  '亲爱的客户，我们已经收到您邮寄回来的智能模块，但是很抱歉，不符合换货条件。'
                ),

                '15' => '您已提交快递单号，物品到达后我们会第一时间处理。'
            ),

        //售后凭证图片全路径
        'supportImgPath' => __DIR__ . '/../public/images/support',
        //售后凭证图片目录
        'supportImgDir' => 'public/images/support',
        //售后凭证图片链接访问地址
        'supportImgAccess' => 'images/support',

        //订单日志操作类型
        'orderActType' =>
            array(
                //添加订单
                'add' => 1,
                //确认订单
                'confirm' => 3,
                //订单失效
                'invalid' => 5,
                //取消订单
                'cancel' => 7,
                //订单分单
                'divide' => 9,
                //交易成功
                'success' => 11,
                //订单发货
                'delivery' => 13,
                //确认收货
                'received' => 15,
                //支付成功
                'successPayment' => 17,
                //编辑订单信息
                'edit' => 19,
                //申请退款
                'back' => 21
            ),

        //售后日志操作类型
        'supportActType' =>
            array(
                //申请售后
                'apply' => 1,
                //审核通过
                'approved' => 3,
                //审核未通过
                'unapproved' => 5,
                //'已解决'
                'resolved' => 7,
                //'已关闭'
                'closed' => 9,
                //已收到用户寄来的鞋子，符合退货或换货条件
                'receiveApprove' => 11,
                //已收到用户寄来的鞋子，但是不符合退货或换货条件
                'receiveUnapproved' => 13,
                //提交快递单号
                'applyExpress' => 15
            ),

        //上传图片大小的最大值 (2M)
        'allowedFileSize' => 1024*1024*2

    );
