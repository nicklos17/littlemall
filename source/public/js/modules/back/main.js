define(function(require){

    var chkAll = require('chkAll');
    var calendar = require('calendar');

    chkAll.run($('#chk-all'), $('.chk-item'));
    calendar.setup('start-time');
    calendar.setup('end-time');

    $('#del-chk').on('click', function(){
        if($('.chk-item:checked').length == 0){
            alert('请先勾选需删除的项');
            return false;
        }
        if(confirm('想好了,确定要删除?')){
            var chkDelArr = $('.chk-item:checked'),
                len       = chkDelArr.length,
                delIdArr  = '';

            for(var i=0; i<len; i++){
                delIdArr += chkDelArr.eq(i).val() + ',';
            }
            $.ajax({
                type: "POST",
                url: "/back/deleteOrders",
                data: {id_arr: delIdArr},
                dataType: "json",
                success: function(msg){
                    if(msg.ret == 1){
                        alert('删除成功');
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

    $('.btn-del').on('click', function(){

        if(confirm('想好了,确定要删除?')){
            var index    = $('.btn-del').index($(this)),
                 bid = $('.chk-item').eq(index).val();

            $.ajax({
                type: "POST",
                url: "/back/deleteOrders",
                data: {id_arr: bid},
                dataType: "json",
                success: function(msg){
                    if(msg.ret == 1){
                        alert('删除成功');
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

    $('#search-clear-bord').on('click', function(){
        $('#bord-sn').val('');
    });

    $('#bord-sn').on('keyup', function(){
        if($(this).val() != ''){
            $('#search-clear-bord').show();
        }
        else{
            $('#search-clear-bord').hide();
        }
    });

    $('#order-sn').on('keyup', function(){
        if($(this).val() != ''){
            $('#search-clear-order').show();
        }else{
            $('#search-clear-order').hide();
        }
    });

    $('#search-clear-order').on('click', function(){
        $('#order-sn').val('');
        $(this).hide();
    });

    $('#search-clear-bord').on('click', function(){
        $('#bord-sn').val('');
        $(this).hide();
    });

    $('#search').on('click', function(){
        $('#back-form').submit();
    });

    $('#set-status').on('click', function(){
        if($('.chk-item:checked').length == 0){
            alert('请先勾选需操作的项');
            return false;
        }
        var chkDelArr = $('.chk-item:checked'),
            len       = chkDelArr.length,
            delIdArr  = '',
            batchStatus = $('#batch-status').val();

        for(var i=0; i<len; i++){
            delIdArr += chkDelArr.eq(i).val() + ',';
        }
        $.ajax({
            type: "POST",
            url: "/back/batchSetStatus",
            data: {id_arr: delIdArr, status: batchStatus},
            dataType: "json",
            success: function(msg){
                if(msg.ret == 1){
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

    var initSel = function(){
        var bordType = $('#bord-type').attr('data') ? $('#bord-type').attr('data') : 0,
            bordStatus = $('#bord-status').attr('data') ? $('#bord-status').attr('data') : 0,
            bordReason = $('#bord-reason').attr('data') ? $('#bord-reason').attr('data') : 0;
        $('#bord-type option[value='+bordType+']').attr('selected', 'selected');
        $('#bord-status option[value='+bordStatus+']').attr('selected', 'selected');
        $('#bord-reason option[value='+bordReason+']').attr('selected', 'selected');
    }

    initSel();
});