<?php

    include_once("../WxPayPubHelper/WxPayPubHelper.php");
    //参数
    $wxConfig = [
        'APPID' => 'wxe0725aa5b1fa7448',
        'MCHID' => '1219205801',
        'KEY' => '74160b347db8b90acc5e52cf5b3c3dee',
        'APPSECRET' => '4f3b4183567672a365653da3cd4444b6',
        'SSLCERT_PATH' => '/srv/www/wechat/WxPayPubHelper/cacert/apiclient_cert.pem',
        'SSLKEY_PATH' => '/srv/www/wechat/WxPayPubHelper/cacert/apiclient_key.pem',
        'NOTIFY_URL' => 'http://wechat.yunduo.com/demo/notify_url.php',
        'CURL_TIMEOUT' => 30
    ];

    //使用统一支付接口
    $unifiedOrder = new UnifiedOrder_pub($wxConfig);
    
    //设置统一支付接口参数
    //设置必填参数
    //appid已填,商户无需重复填写
    //mch_id已填,商户无需重复填写
    //noncestr已填,商户无需重复填写
    //spbill_create_ip已填,商户无需重复填写
    //sign已填,商户无需重复填写
    $unifiedOrder->setParameter("body","贡献一分钱");//商品描述
    //自定义订单号，此处仅作举例
    $timeStamp = time();
    $out_trade_no = $wxConfig['APPID']."$timeStamp";
    $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
    $unifiedOrder->setParameter("total_fee","1");//总金额
    $unifiedOrder->setParameter("notify_url",$wxConfig['NOTIFY_URL']);//通知地址 
    $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
    //非必填参数，商户可根据实际情况选填
    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
    //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
    //$unifiedOrder->setParameter("attach","云朵网络科技");//附加数据 
    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
    //$unifiedOrder->setParameter("goods_tag","云朵网络");//商品标记 
    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
    
    //获取统一支付接口结果
    $unifiedOrderResult = $unifiedOrder->getResult();
    
    //商户根据实际情况设置相应的处理流程
    if ($unifiedOrderResult["return_code"] == "FAIL") 
    {
        //商户自行增加处理流程
        echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
    }
    elseif($unifiedOrderResult["result_code"] == "FAIL")
    {
        //商户自行增加处理流程
        echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
        echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
    }
    elseif($unifiedOrderResult["code_url"] != NULL)
    {
        //从统一支付接口获取到code_url
        $code_url = $unifiedOrderResult["code_url"];
        //商户自行增加处理流程
    }

?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>微信安全支付</title>
</head>
<body>
    <div align="center" id="qrcode">
    </div>
    <div align="center">
        <p>订单号：<?php echo $out_trade_no; ?></p>
    </div>
    <div align="center">
        <form  action="./order_query.php" method="post">
            <input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
            <button type="submit" >查询订单状态</button>
        </form>
    </div>
    <br>
    <div align="center">
        <form  action="./refund.php" method="post">
            <input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
            <input name="refund_fee" type='hidden' value="1">
            <button type="submit" >申请退款</button>
        </form>
    </div>
    <br>
    <div align="center">
        <a href="../index.php">返回首页</a>
    </div>
</body>
    <script src="./qrcode.js"></script>
    <script>
        if(<?php echo $unifiedOrderResult["code_url"] != NULL; ?>)
        {
            var url = "<?php echo $code_url;?>";
            //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
            var qr = qrcode(10, 'M');
            qr.addData(url);
            qr.make();
            var wording=document.createElement('p');
            wording.innerHTML = "扫我，扫我";
            var code=document.createElement('DIV');
            code.innerHTML = qr.createImgTag();
            var element=document.getElementById("qrcode");
            element.appendChild(wording);
            element.appendChild(code);
        }
    </script>
</html>