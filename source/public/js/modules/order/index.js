define(function(require){
    var chkAll = require('chkAll');
    var calendar = require('calendar');

    chkAll.run($('#chk-all'), $('.chk-item'));
    //日历模式
    calendar.setup('start-time');
    calendar.setup('end-time');

    /**
     * [批量编辑订单列表]
     * @return {[type]} [description]
     */
    $('.batch-edit-order').on('click', function(){
        if($('.chk-item:checked').length == 0){
            alert('请先勾选需要操作的项');
            return false;
        }
        var des = $(this).attr('des');
        if(confirm('确定要对这些订单执行"'+des+'"操作？')){
            var oparate = $(this).attr('operate');
            var chkDelArr = $('.chk-item:checked'),
                len = chkDelArr.length,
                orderIds = '',
                index = 0;

            for(var i=0; i<len; i++){
                index  = $('.chk-item').index(chkDelArr[i]);
                orderIds +=  $('.order-id').eq(index).val()+',';
            }
            $.ajax({
                type: "POST",
                url: "/order/batchoperate",
                data: {orderIds: orderIds, operate:$(this).attr('operate')},
                success: function(msg){
                    msg = JSON.parse(msg);
                    if(msg.ret == 1){
                        alert('执行'+des+'操作:共('+ msg.data +')单，操作成功！');
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
        }
    });

/**
 * [设置条件搜索]
 * @return {[type]} [description]
 */
    $('#search').on('click', function(){
        $('#order-search-form').submit();
    });

});