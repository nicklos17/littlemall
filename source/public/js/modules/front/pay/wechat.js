define(function(require){
    var qrcode = require('qrcode').qr;

    //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
    var qr = qrcode(10, 'M');
    qr.addData(url);
    qr.make();
    $('#qrcode').before(qr.createImgTag());
    $('#qrcode').prev('img').addClass('weixin-code');
    //轮询订单状态
    function poolStatus(){
        $.ajax({
            type:"GET",
            dataType:"json",
            url:"/order/poolOrderStatus",
            timeout:5000,
            success:function(data){
                if(data.ret.order_pay_status > 1)
                    window.location.href = '/order';
            }
        });
    }
    setInterval(poolStatus, 5000);
});
