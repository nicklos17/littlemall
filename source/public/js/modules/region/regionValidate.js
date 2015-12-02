define(function(require){

    $.validator.setDefaults(
        {
            messages: {
                'region-name': {
                    required: "请输入地名",
                    maxlength: '地名长度超过最大值'
                },
                'region-code': {
                    maxlength: '地区代码长度超过最大值'
                }
            },
            errorPlacement: function(error, element) {
                $('#bk-error').html(error).show();
            },
            onkeyup: false,
            onclick: false,
            focusCleanup: false,
            onfocusout: false
        }
    );

});