define(function(require){

    var mobileValid = function(phone){
        var phonePattern=/^0?(13[0-9]|15[0-9]|18[0-9]|14[0-9])[0-9]{8}$/;
        if(! phonePattern.exec(phone)){
            return false ;
        }
        else{
            return true ;
        }
    }

    $.validator.setDefaults(
        {
            messages: {
                'order-consignee': {
                    required: "请填写联系人姓名"
                },
                'order-mobi': {
                    required: "请填写联系人手机",
                    maxlength: '手机号码格式错误'
                }
            },
            onkeyup: false,
            onclick: false,
            focusCleanup: false,
            onfocusout: false
        }
    );

    $.validator.addMethod('mobile', function(value, element){
        return mobileValid(value);
    }, '手机号码格式错误');

});