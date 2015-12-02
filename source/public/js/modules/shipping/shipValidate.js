define(function(require){

    $.validator.setDefaults(
        {
            messages: {
                pro_id: {
                    required: "请选择省市区"
                },
                shipping_pay: {
                    required: "请填写快递费用",
                    max: '超过快递费用最大值',
                    digits:'快递费用是整数哦'
                }
            },
            errorPlacement: function(error, element) {
                $('#bk-error').html(error).show();
            },
            onkeyup: false,
            onclick: false,
            focusCleanup: false,
            onfocusout: false,
            debug: true
        }
    );
});