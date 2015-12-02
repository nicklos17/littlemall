define(function(require){

    var region = require('region')
    region.show()
    region.cascade()

    var ajaxPostReq = function(url, data, success){
        $.ajax({
            url:url,
            method:'POST',
            data:data,
            dataType:'json',
            success:success,
            error:function(){
                alert('网络异常,请稍候重试')
            }
        })
    }

    $('#frm-back').validate()

    $('#back-submit').on('click', function(){
        if(!$('#frm-back').valid())
            return false

        var data = $('#frm-back').serialize()
        ajaxPostReq('/back/addOrder', data, function(data){
            if(data.ret == 1){
                alert('success')
            }else{
                alert('failed')
            }
        })
    })

    $('input[name=order_sn]').blur(function(){
        $('#sync').show()
    })

    $('#sync').on('click', function(){
        var orderSn = $('input[name=order_sn]').val()
        ajaxPostReq('/back/syncOrderInfo', {order_sn: orderSn}, function(data){
            if(data.ret == 1){
                $('input[name=mobile]').val(data.orderInfo.order_mobile).trigger('blur')
                $('input[name=order_uid]').val(data.orderInfo.u_id).trigger('blur')
                $('input[name=consignee]').val(data.orderInfo.order_consignee).trigger('blur')
                $('input[name=addr_detail]').val(data.orderInfo.order_addr).trigger('blur')
                var length = data.orderGoods.length,
                    goodsBk = '<option value="">选择商品</option> '
                for(var i=0; i<length; i++){
                    goodsBk += "<option value='" + data.orderGoods[i].ord_goods_id + "'>" + data.orderGoods[i].goods_name + ' ' + data.orderGoods[i].goods_sn
                    var aLen = data.orderGoods[i].attrArr.length
                    for(var j = 0; j < aLen; j++){
                        goodsBk += ' ' + data.orderGoods[i].attrArr[j].type + ':' + data.orderGoods[i].attrArr[j].name
                    }
                    goodsBk += "</option>"
                }
                $('select[name=order_goods_id]').html('').append(goodsBk).trigger('blur')
                $('#provinces').attr('data', data.orderInfo.order_province)
                $('#cities').attr('data', data.orderInfo.order_city)
                $('#districts').attr('data', data.orderInfo.order_district)
                region.show()
            }
        })
    })

})
