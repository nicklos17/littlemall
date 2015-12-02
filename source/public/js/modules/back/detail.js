define(function(require){

    var switchBk = function(hide, show){
        $(hide).hide();
        $(show).show();
    }

    $('#set-status').on('click', function(){
        $('#bord-status option[value='+$('.bk-status').attr('data')+']').prop('selected', 'selected')
        switchBk('.bk-status', '.bk-set-status');
    });

    $('#cancel-status').on('click', function(){
        switchBk('.bk-set-status', '.bk-status');
    });

    $('#confirm-status').on('click', function(){
        var status = $('#bord-status').val();
        var bordId = $('#bord-id').val();
        $.ajax({
            type: "POST",
            url: "/back/setStatus",
            data: {bord_id: bordId, status: status},
            dataType: "json",
            success: function(msg){
                if(msg.ret == '1'){
                    alert('修改成功');
                    location.href = '';
                }
                else{
                    alert('修改失败');
                }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        });
    });

    $('#set-type').on('click', function(){
        $('#bord-type option[value='+$('.bk-type').attr('data')+']').prop('selected', 'selected')
        switchBk('.bk-type', '.bk-set-type');
    });

    $('#cancel-type').on('click', function(){
        switchBk('.bk-set-type', '.bk-type');
    });

    $('#confirm-type').on('click', function(){
        var type = $('#bord-type').val();
        var bordId = $('#bord-id').val();
        $.ajax({
            type: "POST",
            url: "/back/setType",
            data: {bord_id: bordId, bord_type: type},
            dataType: "json",
            success: function(msg){
                if(msg.ret == 1){
                    alert('修改成功');
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

    $('#edit-ship').on('click', function(){
        $('#shipping-sn').val($('.ship-sn-show').html());
        $('#shipping-name').val($('.ship-name-show').html());
        switchBk('.bk-ship', '.bk-set-ship');
    });

    $('#cancel-ship').on('click', function(){
        switchBk('.bk-set-ship, .tips-error', '.bk-ship');
    });

    $('#confirm-ship').on('click', function(){
        if(!$('#ship-form').valid()){
            return false;
        }
        var shippingSn = $('#shipping-sn').val();
        var shippingName = $('#shipping-name').val();
        var bordId = $('#bord-id').val();

        $.ajax({
            type: "POST",
            url: "/back/setShip",
            data: {bord_id: bordId, shipping_sn: shippingSn, shipping_name: shippingName},
            dataType: "json",
            success: function(msg){
                if(msg.ret == 1){
                    alert('修改成功');
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

    $('#edit-money').on('click', function(){
        $('#act-money').val($('.act-money-show').html());
        $('#back-money').val($('.back-money-show').html());
        switchBk('.bk-money', '.bk-set-money');
    });

    $('#cancel-money').on('click', function(){
        switchBk('.bk-set-money, .tips-error', '.bk-money');
    });

    $('#confirm-money').on('click', function(){
        if(!$('#money-form').valid()){
            return false;
        }
        var backMoney = $('#back-money').val();
        var actMoney = $('#act-money').val();
        var bordId = $('#bord-id').val();

        $.ajax({
            type: "POST",
            url: "/back/setActMoney",
            data: {bord_id: bordId, act_money: actMoney, back_money:backMoney},
            dataType: "json",
            success: function(msg){
                if(msg.ret == 1){
                    alert('修改成功');
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