define(function(require){

    var shipNumValid = function(nu){
        return /^[A-Za-z0-9]*$/.test(nu);
    }

    $.validator.setDefaults(
        {
            messages: {
                'shipping-sn': {
                    maxlength: jQuery.format("快递单号不能超过{0}个字符")
                },
                'shipping-name': {
                    maxlength: jQuery.format("快递公司名字不能超过{0}个字符")
                }
            },
            onkeyup: false,
            onclick: false,
            focusCleanup: false,
            onfocusout: false
        }
    );
    $.validator.addMethod('snValid', function(value, element){
        return shipNumValid(value);
    }, '快递单号格式错误');
});