define(function(require){

    /**
     * [根据省市区id显示省市区]
     * @type {[type]}
     */
    var region = require('region');
    region.show(
        function(){
            $('.order-province').html($('#provinces option[selected=selected]').html());
        },
        function(){
            $('.order-city').html($('#cities option[selected=selected]').html());
        },
        function(){
            $('.order-district').html($('#districts option[selected=selected]').html());
        },
        function(){
            $('.order-street').html($('#streets option[selected=selected]').html());
        }
    );
    region.cascade();

    /**
     * [修改订单的产品信息]
     * @return {[type]} [description]
     */
    $('.goods-edit').on('click',function(){
        var attrs = $(this).attr('data').split('-');
        var orderId = attrs[0];
        var goodsId = attrs[1];
        var $attr = $(this).parent().parent(),
            price = $attr.find('.goods-price').html(),
            color = $attr.find('.goods-attr1').html(), colorId = $attr.find('.goods-attr1').attr('data'),
            size = $attr.find('.goods-attr2').html(), sizeId = $attr.find('.goods-attr2').attr('data'),
            num = $attr.find('.goods-num').html();
        $attr.find('.goods-price').html("<input id='goods-price' type='text' value='"+price+"' style='width:100px'/>");
        $attr.find('.goods-attr1').html("<select id='color-selector' style='width:100px' class ='color'/></select>");
        $attr.find('.goods-attr2').html("<select id='size-selector' style='width:100px' class ='size'/></select>");
        $attr.find('.goods-num').html("<input id='goods-num' type='text' value='"+num+"' style='width:100px'/>");
        //获取该商品属性的所有值
        getOrderAttr(goodsId, $attr);
        $(this).parent().append('<a href="javascript:void(0);"  class="btn btn-mini btn-success edit-goods-submit">确认修改</a>');
        $(this).parent().append('<a href="/order/detail?orderId='+orderId+'" class="btn btn-mini btn-success">取消</a>');
        $(this).remove();
    });

    /**
     * [提交商品详情修改]
     * @return {[type]} [description]
     */
    $('body').on('click', '.edit-goods-submit', function(){
        var orderId = $("#order-id").val();
        var $goodsInfo= $(this).parent().parent();
        var orderGoodsId = $goodsInfo.find('.order-goods-id').val();
        var goodsId = $goodsInfo.find('.goods-id').val();
        var colorId = $goodsInfo.find('#color-selector').find('option:selected').val();
        var sizeId = $goodsInfo.find('#size-selector').find('option:selected').val();
        var price = $goodsInfo.find('#goods-price').val();
        var num = $goodsInfo.find('#goods-num').val();
        $.ajax({
            type: "POST",
            url: "/order/editgoods",
            data: {orderGoodsId:orderGoodsId, orderId:orderId, goodsId:goodsId, colorId:colorId, sizeId:sizeId, price:price, goodsNum:num},
            dataType: "json",
            success: function(msg) {
                if(msg.ret == 1){
                    alert('更改成功');
                    window.location='';
                }
                else{
                    yunduoErrorShow(msg.msg);
                }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    });

    /**
     * [编辑订单信息]
     * @return {[type]} [description]
     */
    $('.order-edit').on('click',function(){
        var orderId = $(this).attr('data');
        var $orderInfo = $('.order-info'),
            consignee = $orderInfo.find('.consignee').html(),
            mobi = $orderInfo.find('.mobi').html(),
            addr = $orderInfo.find('.addr').html(),
            shippingType = $orderInfo.find('.shipping-type').html();
        //显示地区选择下拉框
        $orderInfo.find('.region-display').css('display', 'none');
        $orderInfo.find('#region-selector').css('display', 'block');

        shippingTypeId = $orderInfo.find('.shipping-type').attr('data');
        $orderInfo.find('.consignee').html("<input id='order-consignee' name='order-consignee' type='text' class='{required:true}' value='"+consignee+"' style='width:100px'/>");
        $orderInfo.find('.mobi').html("<input id='order-mobi' type='text' name='order-mobi' class='{required:true, maxlength:20, mobile:true}' value='"+mobi+"' style='width:100px'/>");
        $orderInfo.find('.addr').html("<input id='order-addr' type='text' value='"+addr+"' style='width:100px'/>");
        $orderInfo.find('.shipping-type').html("<input id='goods-price' type='text' value='"+shippingType+"' style='width:100px'/>");
        if(shippingTypeId == 1){
            var shippingTypeHtml = "<select class='shipping-type'><option value='1' selected='selected'>快递</option><option value='3'>自取</option></select>";
        }else{
            var shippingTypeHtml = "<select class='shipping-type'><option value='1'>快递</option><option value='3' selected='selected'>自取</option></select>"
        }
        $orderInfo.find('.shipping-type').html(shippingTypeHtml);
        $(this).parent().append('<a href="javascript:void(0);"  class="btn btn-mini btn-success edit-order-submit" order-id="'+orderId+'">确认修改</a>');
        $(this).parent().append('<a href="/order/detail?orderId='+orderId+'" class="btn btn-mini btn-success">取消</a>');
        $(this).remove();
    });

    /**
     * [提交订单信息修改]
     * @return {[type]} [description]
     */
    $('body').on('click', '.edit-order-submit', function(){
        if(!$('#detail-form').valid()){
            return false;
        }
        var orderId = $(this).attr('order-id');
        var consignee = $('.order-info').find('#order-consignee').val();
        var mobi = $('.order-info').find('#order-mobi').val();
        var provinceId = $('.order-info').find('#region-selector').find('#provinces').find('option:selected').val();
        var cityId = $('.order-info').find('#region-selector').find('#cities').find('option:selected').val();
        var districtId = $('.order-info').find('#region-selector').find('#districts').find('option:selected').val();
        var streetId = $('.order-info').find('#region-selector').find('#streets').find('option:selected').val();
        var addr = $('.order-info').find('#order-addr').val();
        var shippingType = $('.order-info').find('.shipping-type').find('option:selected').val();
        $.ajax({
            type: "POST",
            url: "/order/editorder",
            data: {orderId:orderId, mobi:mobi, addr:addr, province:provinceId, city:cityId, district:districtId, street:streetId, shippingType:shippingType, consignee:consignee},
            dataType: "json",
            success: function(msg) {
                if(msg.ret == 1){
                    alert('更改成功！');
                    window.location='';
                }
                else{
                    yunduoErrorShow(msg.msg);
                }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    });

    /**
     * [修改订单状态]
     * @return {[type]} [description]
     */
    $('.edit-status').on('click', function(){
         $.ajax({
            type:"POST",
            url:"/order/editstatus",
            data:{orderId:$('#order-id').val(), operate:$(this).attr('operate')},
            dataType: "json",
            success:function(msg){
                 if(msg.ret == 1){
                     alert('更改成功');
                     location.href = '';
                 }
                 else{
                     yunduoErrorShow(msg.msg);
                 }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
         });
    });

});

/**
 * [根据商品id获取商品的所有属性]
 * @param  {[string]} goodsId [商品id]
 * @param  {[obj]} attrObj [js对象]
 * @return {[type]}         [description]
 */
function getOrderAttr(goodsId, attrObj)
{
    $.ajax({
        type:"POST",
        url:"/order/goodsattr",
        data:{goodsId:goodsId},
        dataType: "json",
        success:function(data){
            var colorLen = data.allColor.length;
            var sizeLen = data.allSize.length;
            var colorSelector = '<select id="color-selector">';
            var sizeSelector = '<select id="size-selector">';
            for(var i=0; i<colorLen; i++)
            {
                colorSelector += "<option value='"+data.allColor[i].attrs_id+"'>"+data.allColor[i].attrs_name+"</option>";
            }

            for(var j=0; j<sizeLen; j++)
            {
                sizeSelector += "<option value='"+data.allSize[j].attrs_id+"'>"+data.allSize[j].attrs_name+"</option>";
            }
            colorSelector += '</select>';
            sizeSelector += '</select>';
            attrObj.find('.goods-attr1').html(colorSelector);
            attrObj.find('.goods-attr2').html(sizeSelector);
        },
        error:function(){
            alert('响应超时,请重新尝试');
        }
    });
}