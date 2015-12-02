define(function(require){
    var isCaptcha = function(captcha){
        var pattern = /^[A-Za-z0-9]{4}$/;
        return pattern.test(captcha);
    }
    $("#changeCode").click(function(){
        var srcdata='/captcha/getCode?tm='+Math.random();
        $('.ver-code-img').attr('src',srcdata);
    });
    $('#confirm').click(function(){
        var yunma = $.trim($('#yunma').val());
        var captcha = $.trim($('#captcha').val());
        if(yunma == ''){
            $('#tips-error-code').show().html('请输入云码');
        }
        else{
            $.ajax({
                type: "POST",
                     url: "/code/checkCode",
                data: 'code='+yunma+'&captcha='+captcha,
                dataType:'json',
                success: function(msg){
                    if(msg.ret == 1){
                        location.href=msg.url;
                    }else{
                        for(var key in msg.msg){
                            if(msg.msg.captcha && msg.msg.captcha.msg == '10045'){
                                $('#bk-captcha').show();
                                break;
                            }
                             $('#tips-error-code').show().html(msg.msg[key].msg);
                        }
                        $('#changeCode').trigger('click');
                    }
                },
                error:function(){
                    alert('服务器出现异常，请稍候重试');
                }
            });
        }
    });
});
