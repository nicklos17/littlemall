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

    $('.shoes-item').on('click', function(){
        if($(this).hasClass('disabled'))
            return

        if(!$(this).hasClass('selected')){
            $('div.shoes-chose').find('li.selected').removeClass('selected')
            $(this).addClass('selected')
            $('input[name=order_goods_id]').val($(this).data('id'))
            $('#error-goods').hide()
        }
    })

    $('.cxt-item').on('click', function(){
        if($(this).hasClass('disabled'))
            return

        if(!$(this).hasClass('selected')){
            $('div.cxt-choose').find('a.selected').removeClass('selected')
            $(this).addClass('selected')
            $('input[name=bord_type]').val($(this).data('id'))
            $('#error-bord-type').hide()
        }
    })

    $('input,textarea').on('focus', function(){
        if(!$(this).hasClass('input-error')){
            $(this).addClass('input-focus')
        }
    }).on('blur', function(){
        $(this).removeClass('input-focus')
    })

    $('#frm-support').validate()

    $('#confirm').on('click', function(){

        $('.tips-error').hide()
        if(!$('input[name=bord_type]').val()){
            $('#error-bord-type').show().html('请选择申请内容')
        }
        if(!$('input[name=order_goods_id]').val()){
            $('#error-goods').show().html('请选择鞋子')
        }

        if(!$('#frm-support').valid())
            return false

        var data = $('#frm-support').serialize()
        ajaxPostReq('/support/addSupport', data, function(data){
            if(data.ret == 1){
                alert('申请成功')
                location.href = '/support'
            }else{
                alert(data.msg)
            }
        })
    })

    var checkTelInput = function(){
        var areaCode = $('input[name=area_code]').val()
        var telNum = $('input[name=tel_num]').val()
        var ext = $('input[name=ext]').val()
        if(!(areaCode || telNum || ext)){
            return ''
        }
        var tel = areaCode + '-' + telNum
        if(ext){
            tel = tel + '-' + ext
        }
        return tel
    }

    $('#frm-support').find('.tel-field').on('keyup', function(){
        $('#tel').val(checkTelInput())
    }).on('blur', function(){
        $('#tel').val(checkTelInput())
    });

    $('.upload').on('change', '#img-evi', function(){
        $.ajaxFileUpload({
            url: '/support/uploadImg?fileInput=1&picPath=' + $('input[name=pic]').val(),
            secureuri: false,
            fileElementId: 'img-evi',
            dataType: 'json',
            success: function(msg) {
                if(msg.ret == 1){
                    $('input[name=pic]').val(msg.path)
                    $('.upload-change').css({"background":"url("+msg.imgUrl + ") no-repeat scroll 0 0 rgba(0, 0, 0, 0)"})
                }else{
                    alert(msg.msg)
                }
            },
            error: function(){
                alert('图片上传失败,请稍后重试')
            }
        })
    })

    $('.upload').on('change', '#add-img-evi', function(){
        $.ajaxFileUpload({
            url: '/support/uploadImg?fileInput=3&picPath=' + $('input[name=pic]').val(),
            secureuri: false,
            fileElementId: 'add-img-evi',
            dataType: 'json',
            success: function(msg) {
                if(msg.ret == 1){
                    $('input[name=pic]').val(msg.path)
                    $('.upload-change').show()
                    $('.upload-change').css({"background":"url("+msg.imgUrl + ") no-repeat scroll 0 0 rgba(0, 0, 0, 0)"})
                    $('.upload-add').hide()
                }else{
                    alert(msg.msg)
                }
            },
            error: function(){
                alert('图片上传失败,请稍后重试')
            }
        })
    })

    $('#edit-img').on('click', function(){
        $('#img-evi').trigger('click')
    })

    $('#add-file-icon').on('click', function(){
        $('#add-img-evi').trigger('click')
    })

    $('#del-img').on('click', function(){
        ajaxPostReq('/support/delImg', {picPath: $('input[name=pic]').val()}, function(data){
            if(data.ret == 1){
                $('.upload-change').css({"background":"none"})
                $('.upload-change').hide()
                $('input[name=pic]').val('')
                $('.upload-add').show()
            }else{
                alert('图片删除失败,请稍候重试')
            }
        })
    })

})