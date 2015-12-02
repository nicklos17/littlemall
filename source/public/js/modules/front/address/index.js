define(function(require){

    $('.address-list').hover(
        function(){
            $(this).find('.address-set').show()
            if($(this).hasClass("default-address")){
                $('#default-badge').hide()
            }
        },
        function(){
            $(this).find('.address-set').hide()
            $('#default-badge').show()
        }
    )

    $('.address-hover').hover(
        function(){
            $(this).addClass('active')
        },
        function(){
            $(this).removeClass('active')
        }
    )

    var $addrBox = $('.address')

    $('.empty-address').on('click', function(){
        region.showProvinces()
        resetForm()
        $('.address-new-tit').html('新增收货地址')
        $('.address-new').fadeIn()
    })

    var region = require('region')
    region.showProvinces()
    region.cascade()

    $('#frm-addr').validate()

    $('input[name=save]').on('click', function(){

        if(!$('#frm-addr').valid())
            return false

        var postUrl;
        if($('input[name=addrId]').val()){
            postUrl = '/address/edit'
        }else{
            postUrl = '/address/add'
        }
        $.ajax({
            url: postUrl,
            method: 'POST',
            data: $('#frm-addr').serialize(),
            dataType:'json',
            success: function(data){
                if(data.ret == 1){
                    location.href = ''
                }else{
                    alert('保存失败,请稍候重试')
                }
            },
            error: function(){
                alert('网络异常,请稍候重试')
            }
        })
        return false
    })

    $('.address-list').on('click', '.edit-addr', function(e){
        var aid = $(this).closest('.address-list').data('id')
        $.ajax({
            url: '/address/getAddr',
            method: 'POST',
            data: {aid:aid},
            dataType:'json',
            success: function(data){
                if(data.ret == 1){
                    $('.address-new-tit').html('编辑收货地址')
                    $('.address-new').fadeIn()
                    var aInfo = data.data
                    $('#provinces').attr('data', aInfo.pro_id)
                    $('#cities').attr('data', aInfo.city_id)
                    $('#districts').attr('data', aInfo.dis_id)
                    $('#streets').attr('data', aInfo.street_id)
                    region.show()
                    if(aInfo.u_addr_tel){
                        $('#tel').val(aInfo.u_addr_tel)
                        var arr = aInfo.u_addr_tel.split('-')
                        $('input[name=areaCode]').val(arr[0])
                        $('input[name=telNum]').val(arr[1])
                        $('input[name=ext]').val(arr[2])
                    }
                    $('textarea[name=detailAddr]').val(aInfo.u_addr_info)
                    $('input[name=zipCode]').val(aInfo.u_addr_zipcode)
                    $('input[name=mobile]').val(aInfo.u_addr_mobile)
                    $('input[name=consignee]').val(aInfo.u_addr_consignee)
                    $('input[name=addrId]').val(aid)
                    if(aInfo.u_addr_default == 3){
                        $('input[name=default]').prop('checked', true)
                    }else{
                        $('input[name=default]').prop('checked', false)
                    }
                }else{
                    alert('加载收货地址失败')
                }
            },
            error: function(){
                alert('加载收货地址失败')
            }
        })
        e.stopPropagation()
    })

    var delAddr = function(addressId) {
        $.post('/address/del', {aid: addressId}, function(data) {
            $('#address-' + addressId).fadeOut(function(){
                if($('input[name=addrId]').val() == addressId){
                    region.showProvinces()
                    resetForm()
                }
                $(this).remove()
            });
        }).fail(function(){
            alert('网络异常,请稍后重试')
        });
    }

    $('.address-list').on('click', '.del-addr', function(e){
        delAddr($(this).closest('.address-list').data('id'))
        e.stopPropagation()
    })

    var addressMenu = '<span class="fr address-set dn"> \
                         <a href="javascript:void(0);" class="set-def">设为默认地址</a> \
                         <em>|</em> \
                         <a href="javascript:void(0);" class="edit-addr">编辑</a> \
                         <em>|</em> \
                         <a href="javascript:void(0)" class="del-addr">删除</a> \
                       </span>';

    var defaultAddressMenu = '<span class="fr address-set dn"> \
                         <a href="javascript:void(0);" class="edit-addr">编辑</a> \
                         <em>|</em> \
                         <a href="javascript:void(0)" class="del-addr">删除</a> \
                       </span>';

    var defaultAddressBadge = '<span class="fr address-default" id="default-badge">默认地址</span>';

    function updateDefAddrMenu(defaultAddrId){
        var $menu = $(addressMenu)
        var $defaultMenu=$(defaultAddressMenu)
        var prevDefault = $('.default-address')
        prevDefault.removeClass('default-address').find('#default-badge').remove()
        $menu.hide()
        prevDefault.find('.address-set').remove()
        prevDefault.find('.address-header').append($menu)
        var $badge = $(defaultAddressBadge)
        $badge.hide()
        $('#address-' + defaultAddrId).find('.address-set').remove()
        $('#address-' + defaultAddrId).addClass('default-address')
            .find('.address-header').append($badge)
            .append($defaultMenu);
        $badge.hide()
    }

    function switchDefAddr(defaultAddrId){
        $.post('/address/setDefault', {aid: defaultAddrId}, function(data){
            if(data.ret == 1){
                updateDefAddrMenu(defaultAddrId);
            }else{
                alert('设置失败,请稍后重试')
            }

        }, 'JSON').fail(function() {
            alert('网络异常,请稍后重试')
        });
    }

    $('.address-list').on('click', '.set-def', function(e){
        switchDefAddr($(this).closest('.address-list').data('id'))
        e.stopPropagation()
    })

    var $modal = $('.new-address-box')

    var resetForm = function(){
        $modal.find('input[type=text], textarea').val('')
        $modal.find('.input-error').removeClass('input-error');
        $modal.find('.tips-error').remove();
        $('#provinces, #cities, #districts, #streets').attr('data', '')
        $('#provinces').html('')
        //region.showProvinces()
        $modal.find('#cities').html('<option value="">请选择城市</option>')
        $modal.find('#districts').html('<option value="">请选择区/县</option>')
        $modal.find('#streets').html('<option value="">请选择街道</option>')
        $('input[name=default]').prop('checked', false)
    }

    $('.cancel').on('click', function(){
        resetForm()
        $('.address-new').fadeOut()
    })

    $('input,textarea').on('focus', function(){
        if(!$(this).hasClass('input-error')){
            $(this).addClass('input-focus')
        }
    }).on('blur', function(){
        $(this).removeClass('input-focus')
    })

    var checkTelInput = function(){
        var areaCode = $('input[name=areaCode]').val()
        var telNum = $('input[name=telNum]').val()
        var ext = $('input[name=ext]').val()
        if(!(areaCode || telNum || ext)){
            return ''
        }
        var tel = areaCode + '-' + telNum;
        if(ext){
            tel = tel + '-' + ext
        }
        return tel
    }

    $('#frm-addr').find('.tel-field').on('keyup', function(){
        $('#tel').val(checkTelInput());
    }).on('blur', function(){
        $('#tel').val(checkTelInput());
    });

})