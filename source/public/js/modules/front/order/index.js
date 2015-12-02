define(function(require){
    $(".confirm-receipt").click(function(){
        var obj = $(this).parent().parent();
        obj.find('.popup').show();
    });

    $(".close").click(function(){
        $('.popup').hide();
    });

    $(".cancel").click(function(){
        var obj = $(this).parent().parent().parent().parent().parent();
        obj.find('.popup').hide();
    });

    //确认收货
    $(".confirm").click(function(){
        var obj = $(this).parent().parent().parent().parent().parent();
        var sn = $(this).attr('sn');
        if(sn != ''){
            $.ajax({
                type: "POST",
                url: "/order/confirmOrder",
                data: 'sn='+sn,
                timeout : 10000,
                dataType:'json',
                success: function(msg) {
                    if(msg.ret == 1){
                        obj.find('.popup').hide();
                        location.href="";
                    }
                    else
                    {
                        for(var key in msg.msg){
                            alert(msg.msg[key].msg);
                        }
                    }
                },
                error:function(){
                    alert('服务器出现异常，请稍候重试');
                }
            });
        }
    });

    $('.table-data').hover(
        function(){
            $(this).find('img').addClass('active');
            $(this).find('.order-item-name').addClass('active');
        },
        function(){
            $(this).find('img').removeClass('active');
            $(this).find('.order-item-name').removeClass('active');
        }
    )
});