define(function(require){
    resizeWindow();
    var chkAll = require('chkAll');
    chkAll.run($('#chk-all'), $('.chk-item'));
    //商品删除
    $('.item-del').on('click', function(){
        if(confirm('确认此商品?'))
        {
            var $item = $(this).parent().parent(), id =$item.find('.goos-id').val();
            $.ajax({
                type: "POST",
                url: "/item/delitem",
                data: {goodsId:id},
                dataType: "json",
                success: function(msgObj){
                    if(msgObj.ret == 1)
                        window.location.href = '';
                    else
                        yunduoErrorShow(msgObj.msg);
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            });
        }else
            return false;
    })

    //批量删除
    $('#del-chk').on('click', function(){

        if(confirm('想好了,确定要删除?')){
            var chkDelArr = $('.chk-item:checked'),
                len = chkDelArr.length,
                delIdArr  = '';

            for(var i=0; i<len; i++){
                delIdArr += chkDelArr.eq(i).val() + ',';
            }
            if(delIdArr){
                $.ajax({
                    type: "POST",
                    url: "/item/batchdelgoods",
                    data: {goodsIds: delIdArr},
                    dataType: "json",
                    success: function(msgObj){
                    if(msgObj.ret == 1)
                        window.location.href = '';
                    else
                        yunduoErrorShow(msgObj.msg);
                    },
                    error:function(){
                        alert('响应超时,请重新尝试');
                    }
                });
            }
        }
    });

    $('#search').on('click', function(){
        var goodsStatus = $('#goods-status').val(), goodsSn = $('#goods-sn').val(), goodsName = $('#goods-name').val();
        var params = '';
        if(!goodsSn && !goodsName && goodsStatus == 0){
            window.location.href = '/item';
            return ;
        }
        if(goodsStatus > 0) params += '&goods_status='+goodsStatus;
        if(goodsSn) params += '&goods_sn='+goodsSn;
        if(goodsName) params += '&goods_name='+goodsName;
        if(params != ''){
            location.href = '/item/index' + params.replace('&','?');
        }
    });
})
