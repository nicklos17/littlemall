define(function(require){

    $.validator.addMethod('isZipCode', function(value, element) {
        var reg = /^[0-9]{6}$/
        return reg.exec(value)
    }, '邮政编码格式不正确')

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
        var flag = /^[0\+]\d{2,3}$/.test($('input[name=areaCode]').val())
        flag = flag && /^\d{7,8}$/.test($('input[name=telNum]').val())
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
    }, '请选择省市区');

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
                }
            }
        }
    )

    $.validator.addClassRules('tel-field', {
        'isTel' : true
    });

    $.validator.addClassRules('required_group', {
        'required_group' : true
    });

    $.validator.addClassRules('region', {
        'regionValid' : true
    });

    jQuery.validator.messages.required_group = '电话号码、手机号码必须填一项'

})