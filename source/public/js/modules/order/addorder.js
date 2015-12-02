define(function(require){

    var region = require('region');

    region.showProvinces();
    region.cascade();

    //编辑器初始化
    var um = UM.getEditor('good-desc');
    $("input[type='checkbox']").prop('checked', false);

    /**
     * [添加商品按钮]
     * @return {[type]} [description]
     */
    $('#add-goods-btn').on('click', function(){
        //标志为1,可添加商品
        if($('#add-flag').val() == '1'){
            $('#add-order-tb .order-goods-body').append('<tr class="order-goods-info">'+$('.add-goods').clone().html()+'</tr>');
            $('#add-flag').val('3');
        }else{
            return false;
        }
    });

    /**
     * [填入商品名称获得商品信息]
     * @return {[type]} [description]
     */
    $('#add-order-tb').on('click', '.check-goods', function(){
        var $goods = $(this).parent().parent();
        var goodsName = $goods.find('.goods-name').val();
        if(!goodsName){
            alert('请输入商品名称');return false;
        }
        $.ajax({
            type:"POST",
            url:'/order/getgoods',
            data:{goodsName:$goods.find('.goods-name').val()},
            dataType: "json",
            success:function(data){
                if(data.ret == 1 && data.data){
                    $('#yunduo-error-msg').empty();
                    $goods.find('.goods-id').val(data.data['goods_id']);
                    $goods.find('.goods-sn').html(data.data['goods_sn']);
                    $goods.find('.goods-name').val(data.data['goods_name']);
                    $goods.find('.market-price').html(data.data['goods_market']);
                    $goods.find('.show-goods-price').html("<input type='text' class='goods-price' value='"+data.data['goods_price']+"'>");
                    var cLen = data.data['allColor'].length;
                    var sLen = data.data['allColor'].length;
                    var colorSelector = '<select class="color-selector" style="width:100px;">';
                    var sizeSelector = '<select class="size-selector" style="width:100px;">';
                    for(var i=0; i<cLen; i++)
                    {
                        colorSelector += "<option value='"+data.data.allColor[i].attrs_id+"'>"+data.data.allColor[i].attrs_name+"</option>";
                    }

                    for(var j=0; j<sLen; j++)
                    {
                        sizeSelector += "<option value='"+data.data.allSize[j].attrs_id+"'>"+data.data.allSize[j].attrs_name+"</option>";
                    }
                    colorSelector += '</select>';
                    sizeSelector += '</select>';
                    $goods.find('.goods-color').html(colorSelector);
                    $goods.find('.goods-size').html(sizeSelector);
                }else{
                    yunduoErrorShow(data.msg);
                }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        });
    });

    /**
     * [确认添加商品]
     * @return {[type]} [description]
     */
    $('#add-order-tb').on('click', '.goods-makesure', function(){
        var $goodsInfo = $(this).parent().parent(),
         colorId = $goodsInfo.find('.color-selector').find('option:selected').val(),
            sizeId = $goodsInfo.find('.size-selector').find('option:selected').val();

        if(colorId == 0 || sizeId == 0){
            alert('请选择商品属性');
            return false;
        }

        var goodsId = $goodsInfo.find('.goods-id').val(),
            goodsName = $goodsInfo.find('.goods-name').val(),
            goodsNum = parseInt($goodsInfo.find('.goods-num').val()),
            goodsPrice = parseFloat($goodsInfo.find('.goods-price').val()).toFixed(2),
            marketPrice = parseFloat($goodsInfo.find('.market-price').html()).toFixed(2),
            colorName = $goodsInfo.find('.color-selector').find('option:selected').html(),
            sizeName = $goodsInfo.find('.size-selector').find('option:selected').html(),
            oldTotalFee = parseFloat($('#order-total-fee').html()).toFixed(2),
            oldOrderFee = parseFloat($('#order-pay-fee').val()).toFixed(2);
            $.ajax({
                type:"POST",
                url:"/order/checkgoods",
                data:{goodsId:goodsId, colorId:colorId, sizeId:sizeId},
                dataType:"json",
                success:function(msg){
                    if(msg.ret == 1){
                        $('#yunduo-error-msg').empty();
                        //alert('添加成功');
                        $goodsInfo.find('.display-name').html(goodsName);
                        $goodsInfo.find('.goods-color').attr('data', colorId);
                        $goodsInfo.find('.goods-color').html(colorName);
                        $goodsInfo.find('.goods-size').attr('data', sizeId);
                        $goodsInfo.find('.goods-size').html(sizeName);
                        $goodsInfo.find('.show-goods-num').html(goodsNum);
                        $goodsInfo.find('.show-goods-price').html(goodsPrice);
                        $goodsInfo.find('.goods-del').attr('data', '3');
                        $('#add-flag').val('1');
                        $goodsInfo.find('.operate-btn').find('.goods-makesure').css('display', 'none');
                        $('#order-total-fee').html(parseFloat(Number(goodsNum * marketPrice) + Number(oldTotalFee)).toFixed(2));
                        $('#order-pay-fee').val(parseFloat(Number(goodsNum * goodsPrice) + Number(oldOrderFee)).toFixed(2));
                    }else{
                        yunduoErrorShow(msg.msg);
                    }
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            });
    });

    /**
     * [删除添加的商品]
     * @return {[type]} [description]
     */
    $('#add-order-tb').on('click', '.goods-del', function(){
        if($(this).attr('data') == 1){
            $('#add-flag').val('1');
        }

        $(this).parent().parent().remove();
        $(this).remove();
    });

    /**
     * [创建订单并入库]
     * @return {[type]} [description]
     */
    $('#create-order').on('click', function(){
        if(!$('#add-order-form').valid()){
            return false;
        }
        //获取添加商品的类型数量
        var nums = $('.order-goods-info').size();
        var goodsData = {};
        for(var i = 0; i < nums; i++){
            if($('.order-goods-info').eq(i).find('.goods-id').val() != ''){
                var goodsInfo = {};
                goodsInfo['goodsId'] = $('.order-goods-info').eq(i).find('.goods-id').val();
                goodsInfo['colorId'] = $('.order-goods-info').eq(i).find('.goods-color').attr('data');
                goodsInfo['sizeId'] = $('.order-goods-info').eq(i).find('.goods-size').attr('data');
                goodsInfo['goodsNum'] = $('.order-goods-info').eq(i).find('.show-goods-num').html();
                goodsInfo['marketPrice'] = $('.order-goods-info').eq(i).find('.shipping-price').html();
                goodsInfo['marketPrice'] = $('.order-goods-info').eq(i).find('.market-price').html()
                goodsInfo['goodsPrice'] = $('.order-goods-info').eq(i).find('.show-goods-price').html();
                goodsData[i] = goodsInfo;
            }
        }
        $.ajax({
            type:'POST',
            url:'/order/create',
            data:{goodsData:goodsData,consignee:$('#consignee').val(),mobi:$('#mobi').val(),province:$('#provinces').val(),city:$('#cities').val(),district:$('#districts').val(),street:$('#streets').val(),addr:$('#addr').val(),shippingFee:$('#shipping-fee').val(),orderFee:$('#order-pay-fee').val()},
            dataType:'json',
            success:function(msg){
                if(msg.ret == 1){
                    $('#yunduo-error-msg').empty();
                    alert('订单创建成功,返回订单列表');
                    location.href = "/order";
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