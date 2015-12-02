define(function(require){

    $('.cate_del').on('click', function(){
        if(confirm('确认删除?'))
        {
            var $cate=$(this).parent().parent(), cateId=$cate.find('.cate_id').html();
            $.ajax({
                type: "POST",
                url: "/category/delcate",
                data: {cid:cateId},
                dataType:'json',
                success: function(msgObj){
                    if(msgObj.ret == 1){
                        $cate.remove();
                        $('#yunduo-error-msg').empty();
                    }
                    else
                        yunduoErrorShow(msgObj.msg);
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            });
        }else
            return false;
    })
});