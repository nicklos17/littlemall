define(function(require){

    var ajaxPostReq = function(url, data, success){
        $.ajax({
            url:url,
            method:'POST',
            data:data,
            dataType:'json',
            success:success,
            error:function(){
                alert('操作失败,请稍候重试')
            }
        })
    }

    $('#confirm').on('click', function(){
        $('.tips-error').hide()
        var shipComp = $('select[name=ship_comp]').val()
        if($('select[name=ship_comp]').val() == 1){
            shipComp = $('input[name=ship_other_comp]').val()
        }
        var shipSn = $.trim($('input[name=ship_sn]').val())
        if(!shipComp){
            $('#error-ship-comp').show().html('请填写快递公司名称')
        }
        if(!shipSn){
            $('#error-ship-sn').show().html('请填写快递单号')
            return false
        }
        if(shipSn.length < 5){
            $('#error-ship-sn').show().html('快递单号一般不少于5个字符，请检查')
            return false
        }
        if(shipSn.length > 26){
            $('#error-ship-sn').show().html('快递单号过长，请检查')
            return false
        }
        ajaxPostReq(
            '/support/submitExpress',
            {ship_comp: shipComp, ship_sn: $('input[name=ship_sn]').val()},
            function(data){
                if(data.ret == 1){
                    alert('提交快递单号成功')
                    location.href = '/support'
                }else{
                    alert(data.msg)
                }
            }
        )
    })

})