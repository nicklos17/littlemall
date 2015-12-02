define(function(require){

    var region = require('region');

    region.showProvinces();
    if($('#provinces').attr('data')){
        region.showCities()
    }
    if($('#cities').attr('data')){
        region.showDistricts();
    }
    region.cascade();

    var showError = function(msg){
        $('#yunduo-error-msg').append('<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">×</a><strong>错误: </strong> '+msg+'</div>');
    };

    var chkAll = require('chkAll');
    chkAll.run($('#chk-all'), $('.chk-item'));

    var showAddErr = function(msg){
        $('#bk-error').show().html(msg);
    }

    $('#add-rule').on('click', function(){
        var proId    = $('#provinces').val(),
            cityId   = $('#cities').val() ? $('#cities').val() : 0,
            disId    = $('#districts').val() ? $('#districts').val() : 0,
            fee      = $.trim($('#fee').val());
        $('#bk-error').hide();
        if(!(proId)){
            showAddErr('请选择省(市,区)');
            return false;
        }
        if(!fee){
            showAddErr('请填写快递费用');
            return false;
        }
        if(fee.match(/\D/)){
            showAddErr('快递费用格式错误');
            return false;
        }
        if(Number(fee) > 127){
            showAddErr('超过快递费用最大值');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/shipping/addRule",
            dataType: 'json',
            data: {pro_id:  proId, city_id: cityId, dis_id: disId, fee: fee},
            success: function(msg) {
                if(msg.ret == 1){
                    alert('添加成功');
                    //location.href = '/shipping&pro_id='+proId+'&city_id='+cityId+'&dis_id='+disId;
                    location.href = '';
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

    $('.edit').on('click', function(){
        $('.txt-edit').parent().html($('.txt-edit').attr('data'));
        var nameObj    = $('.fee').eq($('.edit').index($(this)));
        nameObj.html(
            '<input type="text" class="txt-edit" data="'+nameObj.html()+
                '" value="'+nameObj.html()+'"/>'
        );
        $('.txt-edit').focus();
    });

    $('#ship-tb').on('blur', '.txt-edit', function(){
        var fee    = $.trim($('.txt-edit').val()),
            index  = $('.fee').index($(this).parent()),
            proId  = $('.pro').eq(index).attr('data'),
            cityId = $('.city').eq(index).attr('data'),
            disId  = $('.dis').eq(index).attr('data');
        $('#yunduo-error-msg').empty();
        if(fee == ''){
            showError('请填写快递费用');
            $('.txt-edit').focus();
            return false;
        }
        if(fee.match(/\D/)){
            showError('快递费用格式错误');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/shipping/editRule",
            dataType: 'json',
            data: {pro_id: proId, city_id: cityId, dis_id: disId, fee: fee},
            success: function(msg) {
                if(msg.ret == 1){
                    $('#yunduo-error-msg').empty();
                    $('.txt-edit').parent().html(fee);
                }
                else{
                    yunduoErrorShow(msg.msg);
                }
            },
            error:function(){
                alert('更新失败，请稍候重试');
            }
        });
    });

    $('.delete').on('click', function(){
        if(confirm('想好了,确定要删除?')){
            var index  = $('.delete').index($(this)),
                idArr =  $('.pro').eq(index).attr('data') + ',' + $('.city').eq(index).attr('data') + ',' + $('.dis').eq(index).attr('data');
            $.ajax({
                type: "POST",
                url: "/shipping/deleteRules",
                dataType:'json',
                data: {id_arr: idArr},
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

    $('#del-rules').on('click', function(){
        if($('.chk-item:checked').length == 0){
            alert('请先勾选需删除的项');
            return false;
        }
        if(confirm('想好了,确定要删除?')){
            var chkDelArr = $('.chk-item:checked'),
                len       = chkDelArr.length,
                delIdArr  = '',
                index     = 0;

            for(var i=0; i<len; i++){
                index  = $('.chk-item').index(chkDelArr[i]);
                delIdArr +=  $('.pro').eq(index).attr('data') + ',' + $('.city').eq(index).attr('data') + ',' + $('.dis').eq(index).attr('data')+'|';
            }
            $.ajax({
                type: "POST",
                url: "/shipping/deleteRules",
                dataType: 'json',
                data: {id_arr: delIdArr},
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

    $('#search').on('click', function(){
        $('#ship-form').submit();
    });
});