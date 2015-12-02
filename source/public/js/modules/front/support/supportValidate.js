define(function(require){

    $.validator.addMethod('isMobile', function(value, element){
        var length = value.length
        var mobile = /^1[3,4,5,7,8]+\d{9}$/
        var $mobile = $('input[name=mobile]')
        if(!$mobile.is(':filled')){
            return true
        }
        return length == 11 && mobile.exec(value)
    }, '手机号码格式不正确')

    $.validator.addMethod('isTel', function(value, element){
        if(!$('#tel').is(':filled')){
            return true
        }
        //var flag = /^[0\+]\d{2,3}$/.test($('input[name=area_code]').val())
        flag = /^\d{7,8}$/.test($('input[name=tel_num]').val())
        if($('input[name=ext]').val()){
            flag = flag && /^\d{1,}$/.test($('input[name=ext]').val())
        }
        return flag
    }, '固定电话格式错误')

    $.validator.addMethod('required_group', function(val, el){
        var $module = $(el).parent()
        return $module.find('.required_group:filled').length
    })

    $.validator.addMethod('regionValid', function(value, element) {
        if(!$(element).val()){
            return false
        }
        return true
    }, '请选择省市区')

    $.validator.setDefaults(
        {
            errorClass: 'input-error',
            errorElement: 'span',
            errorPlacement: function(error, element){
                error.addClass('tips tips-error')
                var placement = element.closest('.reg-row')
                placement.find('.tips-error').remove()
                placement.append(error)
            },
            rules: {
                detailAddr: {
                    required: true,
                    minlength:4,
                    maxlength: 120
                },
                zipCode: {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                    isZipCode: true
                },
                mobile: {
                    isMobile: true
                },
                consignee:{
                    required: true,
                    maxlength: 15
                },
                tel: {
                    isTel: true
                },
                bord_msg: {
                    required: true,
                    minlength: 6,
                    maxlength: 300
                },
                goods_num: {
                    required: true,
                    min:1
                },
                bord_reason: {
                    required: true
                },
                addr_detail: {
                    required: true,
                    minlength:4,
                    maxlength: 120
                },
                bord_type: {
                    required: true
                },
                order_goods_id: {
                    required: true
                }
            },

            messages: {
                'detailAddr': {
                    required: '请填写详细地址'
                },
                'zipCode': {
                    required: '请填写邮政编码'
                },
                'consignee': {
                    required: "请填写收货人姓名",
                    maxlength: "收货人姓名请保持在15个汉字以内"
                },
                'bord_msg': {
                    required: "请填写情况描述",
                    minlength: "最少填写6个汉字,最多300个汉字",
                    maxlength: "最少填写6个汉字,最多300个汉字"
                },
                'goods_num': {
                    required: "请填写申请数量",
                    min: "申请数量不能少于1"
                },
                'bord_reason': {
                    required: "请选择申请原因"
                },
                'addr_detail': {
                    required: '请填写详细地址'
                },
                'bord_type': {
                    required: '请选择申请内容'
                },
                'order_goods_id': {
                    required: '请选择鞋子'
                }
            }
        }
    )

    $.validator.addClassRules('tel-field', {
        'isTel' : true
    })

    $.validator.addClassRules('required_group', {
        'required_group' : true
    })

    $.validator.addClassRules('region', {
        'regionValid' : true
    })

    jQuery.validator.messages.required_group = '电话号码、手机号码必须填一项'

})