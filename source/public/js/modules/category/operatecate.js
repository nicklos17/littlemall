define(function(require){

    $('#cate-submit').on('click', function(){
        var url;
        if(!$('#cate-form').valid()){
            return false;
        }
        if($('#cid').val())
            url = '/category/editcate';
        else
            url = '/category/addcate';

        $.ajax({
            type: "POST",
            url: url,
            data: $('#cate-form').serialize(),
            dataType:'json',
            success: function(msgObj){
                if(msgObj.ret == 1)
                    location.href = "/category"
                else
                    yunduoErrorShow(msgObj.msg);
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        });
    })
});