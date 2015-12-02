define(function(require){

    var region = require('region');

    region.showProvinces(function(){
        refreshTb('provinces');
    });

    var showError = function(msg){
        $('#yunduo-error-msg').append('<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">×</a><strong>错误: </strong> '+msg+'</div>');
    };

    var refreshTb = function(type){
        var proOptArray = $('#'+type+' option'),
            length      = proOptArray.length,
            itemBk      = '';
        $('.item').remove();
        for(var i=1; i<length; i++){
            itemBk +=
                '<tr class="item">'+
                    '<td class="center"><label><input type="checkbox" class="chk-item" /> <span class="lbl"></span></label></td>'+
                    '<td class="name center" data="' + proOptArray.eq(i).val()+ '">' + proOptArray.eq(i).html() + '</td>' +
                    '<td class="code center" data="' + proOptArray.eq(i).attr('data')+ '">' + proOptArray.eq(i).attr('data') + '</td>' +
                    '<td class="center">' +
                    '<span class="op-edit"><button class="btn btn-mini edit" data="' + (i-1) + '">'+
                    '<i class="icon-pencil"></i></button>&nbsp;'+
                    '<button class="btn btn-mini delete">'+
                    '<i class="icon-trash"></i></button></span>'+
                    '<span class="op-confirm dn"><button class="btn btn-mini confirm" data="' + (i-1) + '">'+
                    '确定</button>&nbsp;'+
                    '<button class="btn btn-mini cancel">'+
                    '取消</button></span>'+
                    '</td>'+
                '</tr>';
        }
        $('#region-tb').append(itemBk);
        //$('html, body').scrollTop(0);
        resizeWindow();
    }

    var refCrumbs = function(type){
        switch (type) {
            case 0:
                $('#cr-province, #cr-city, #cr-district, #cr-street').remove();break;
            case 1:
                $('#cr-province, #cr-city, #cr-district, #cr-street').remove();
                $('#crumbs').append(
                    '<a href="javascript:void(0);" id="cr-province" class="label label-large label arrowed arrowed-right arrowed-in">'+$('#provinces option[value='+$('#provinces').val()+']').html() +'</a>'
                );break;
            case 2:
                $('#cr-city, #cr-district, #cr-street').remove();
                $('#crumbs').append(
                    '<a href="javascript:void(0);" id="cr-city" class="label label-large label arrowed arrowed-right arrowed-in">'+$('#cities option[value='+$('#cities').val()+']').html() +'</a>'
                );break;
            case 3:
                $('#cr-district, #cr-street').remove();
                $('#crumbs').append(
                    '<span id="cr-street" class="label label-large label arrowed arrowed-right arrowed-in">' + $('#districts option[value='+$('#districts').val()+']').html() + '</span>'
                );break;
            default :break;
        }
    }

    var ajaxRefTb = function(regionType){
        switch(regionType){
            case '1':
                region.showProvinces(function(){
                    refreshTb('provinces');
                });break;
            case '2':
                region.showCities(function(){
                    refreshTb('cities');
                });break;
            case '3':
                region.showDistricts(function(){
                    refreshTb('districts');
                });break;
            case '4':
                region.showStreets(function(){
                    refreshTb('streets')
                });break;
            default :location.href = '';
        }
    }

    $('#cr-country').on('click', function(){
        $('#region-type').val('1');
        $('#cities, #districts, #streets').html('');
        refreshTb('provinces');
        refCrumbs(0);
        $('#provinces').find('option:first').prop('selected', true);
    });

    $('#crumbs').on('click', '#cr-province', function(){
        $('#districts, #streets').html('');
        refreshTb('cities');
        refCrumbs(1);
        $('#cities').find('option:first').prop('selected', true);
    });

    $('#crumbs').on('click', '#cr-city',function(){
        $('#streets').html('');
        refreshTb('districts');
        refCrumbs(2);
        $('#districts').find('option:first').prop('selected', true);
    });

    $('#crumbs').on('click', '#cr-district',function(){
        refreshTb('streets');
        refCrumbs(3);
        $('#streets').find('option:first').prop('selected', true);
    });

    $('#provinces').on('change', function(){
        var proId = $(this).val();
        if(!proId) return false;
        $('#pro-id').val(proId);
        $('#cities, #districts, #streets').html('');
        $('#region-type').val('2');
        $(this).attr('data', proId);
        region.showCities(function(){
            refreshTb('cities');
            refCrumbs(1);
        });
    });

    $('#cities').on('change', function(){
        var cityId = $(this).val();
        if(!cityId) return false;
        $('#city-id').val(cityId);
        $('#region-type').val('3');
        $('#districts, #streets').html('');
        $(this).attr('data', cityId);
        region.showDistricts(function(){
            refreshTb('districts');
            refCrumbs(2);
        });
    });

    $('#districts').on('change', function(){
        var disId = $(this).val();
        if(!disId) return false;
        $('#region-type').val('4');
        $('#dis-id').val(disId);
        $('#streets').html('');
        $(this).attr('data', disId);
        region.showStreets(function(){
            refreshTb('streets');
            refCrumbs(3);
        });
    });

    $('#region-tb').on('click', '.edit', function(){
        //$('.name-edit').parent().html($('.name-edit').attr('data'));
        var i = $(this).attr('data');
        var nameObj = $('.name').eq(i);
        var codeObj = $('.code').eq(i);
        nameObj.html(
            '<input type="text" class="name-edit" data="'+nameObj.html()+
                '" value="'+nameObj.html()+'"/>'
        );
        codeObj.html(
            '<input type="text" class="code-edit" data="'+codeObj.html()+
                '" value="'+codeObj.html()+'"/>'
        );
        $('.op-edit').eq(i).hide();
        $('.op-confirm').eq(i).show();
        $('.name-edit').eq(i).focus();
    });

    $('#region-tb').on('click', '.cancel', function(){
        var i = $('.cancel').index($(this));
        var nameObj = $('.name').eq(i);
        var codeObj = $('.code').eq(i);
        nameObj.html(nameObj.find('.name-edit').attr('data'));
        codeObj.html(codeObj.find('.code-edit').attr('data'));
        $('.op-edit').eq(i).show();
        $('.op-confirm').eq(i).hide();
    });

    $('#region-tb').on('click', '.confirm', function(){
        var i = $('.confirm').index($(this)),
            nameObj = $('.name').eq(i),
            codeObj = $('.code').eq(i),
            name = $.trim(nameObj.find('.name-edit').val()),
            code = $.trim(codeObj.find('.code-edit').val()),
            regionId   = nameObj.attr('data'),
            regionType = $('#region-type').val();

        $('#yunduo-error-msg').empty();
        if(name == ''){
            showError('请填写地名');
            return false;
        }
        if(name.length > 30){
            showError('地名长度超过最大值');
            return false;
        }
        if(code.length > 30){
            showError('地区代码长度超过最大值');
            return false;
        }
        if(code.match(/\D/) != null){
            showError('地区代码格式错误');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/region/editRegion",
            dataType: 'json',
            data: {type: regionType, name: name, code: code, region_id: regionId},
            success: function(msg) {
                if(msg.ret == 1){
                    $('.op-edit').eq(i).show();
                    $('.op-confirm').eq(i).hide();
                    $('#yunduo-error-msg').empty();
                    nameObj.html(name);
                    codeObj.html(code);
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

    var showAddErr = function(msg){
        $('#bk-error').show().html(msg);
    }

    $('#add-region').on('click', function(){
        $('#bk-error').hide();
        var regionName = $.trim($('#region-name').val()),
            regionType = $('#region-type').val(),
            regionCode = $.trim($('#region-code').val()),
            proId      = $('#pro-id').val(),
            cityId     = $('#city-id').val(),
            disId      = $('#dis-id').val();
        if(regionName == ''){
            showAddErr('请填写地名');
            return false;
        }
        if(regionName.length > 30){
            showAddErr('地名长度超过最大值');
            return false;
        }
        if(regionCode.length > 30){
            showAddErr('地区代码长度超过最大值');
            return false;
        }
        if(regionCode.match(/\D/) != null){
            showAddErr('地区代码格式错误');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/region/addRegion",
            dataType: 'json',
            data: {type: regionType, name: regionName, pro_id: proId, city_id: cityId, dis_id: disId, code: regionCode},
            success: function(msg) {
                if(msg.ret == 1){
                    $('#region-name, #region-code').val('');
                    ajaxRefTb(regionType);
                }
                else{
                    alert('添加失败,请重试')
                }
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    });

    $('#chk-all').on('click', function(){
        if($('#chk-all').is(':checked')){
            $('.chk-item').each(function(){
                this.checked = true;
            });
        }
        else{
            $('.chk-item').each(function(){
                this.checked = false;
            });
        }
    });

    $('#region-tb').on('click', '.chk-item', function(){
        if(! $(this).is(':checked')){
            $('#chk-all').attr('checked', false);
        }
        else{
            if($('.chk-item').length == $('.chk-item').filter(':checked').length){
                $('#chk-all').prop('checked', true);
            }
        }
    });

    $('#region-tb').on('click', '.delete', function(){
        if(confirm('想好了,确定要删除?')){
            var index    = $('.delete').index($(this)),
                regionId = $('.name').eq(index).attr('data'),
                type     = $('#region-type').val();

            $.ajax({
                type: "POST",
                url: "/region/deleteRegion",
                data: {type: type, reg_id_arr: regionId},
                dataType: 'json',
                success: function(msg){
                    if(msg.ret == '1'){
                        ajaxRefTb(type);
                    }
                    else{
                        alert('删除失败');
                    }
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            });
        }
    });

    $('#del-sel').on('click', function(){
        if($('.chk-item:checked').length == 0){
            alert('请先勾选需删除的项');
            return false;
        }
        if(confirm('想好了,确定要删除?')){
            var chkDelArr = $('.chk-item:checked'),
                type      = $('#region-type').val(),
                len       = chkDelArr.length,
                delIdArr  = '',
                index     = 0;

            for(var i=0; i<len; i++){
                index  = $('.chk-item').index(chkDelArr[i]);
                delIdArr += $('.name').eq(index).attr('data')+',';
            }
            $.ajax({
                type: "POST",
                url: "/region/deleteRegion",
                dataType: 'json',
                data: {type: type, reg_id_arr: delIdArr},
                success: function(msg){
                    if(msg.ret == '1'){
                        ajaxRefTb(type);
                    }
                    else{
                        alert('删除失败');
                    }
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            });
        }
    });
});