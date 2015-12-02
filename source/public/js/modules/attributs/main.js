define(function(require){

    $('.attr_edit').on('click',function(){
        var $attr=$(this).parent().parent(), val=$attr.find('.attr_name').html(), rank = $attr.find('.attr_rank').html(),
        status = $attr.find('.attr_rank').html();
        $attr.find('.attr_name').html("<input type='text' value='"+val+"' class='edit-name-a' name='edit_name' style='width:50px'/>");
        $attr.find('.attr_rank').html("<input type='text' value='"+rank+"' class='edit-rank-a' name='edit_rank' style='width:50px'/>");
        if(status != '正常')
        {
            $attr.find('.attr-status').html("<select><option value = '1' >正常</option></select>");
        }
        $(this).parent().append('<a href="javascript:void();"  class="btn btn-mini btn-success attr_edit_a">确认修改</a>');
        $(this).remove();
    });

    $('body').on('click', '.attr_edit_a', function(){
        var $attr = $(this).parent().parent(), attrId = $attr.find('.attr_id').html(), status = 1, 
        name = $attr.find('.edit-name-a').val(), rank = $attr.find('.edit-rank-a').val();
        if(!name){
            yunduoErrorShow({'name':{'msg':"属性名不能为空"}});
            return false;
        }
        if(!/^\d+$/.test(rank)){
            yunduoErrorShow({'name':{'msg':"错误的排序,请输入整数,值越大,排行越靠前"}});
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/attributes/editattr",
            data: {aid:attrId, name:name, status:status, rank:rank},
            dataType:'json',
            success: function(msgObj){
                if(msgObj.ret == 1)
                    location.href = '';
                else
                    yunduoErrorShow(msgObj.msg);
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    });

    $('.attr_del').on('click', function(){
        if(confirm('确认删除?'))
        {
            var $attr=$(this).parent().parent(), attrId=$attr.find('.attr_id').html();
            $.ajax({
                type: "POST",
                url: "/attributes/delattr",
                data: {aid:attrId},
                dataType:'json',
                success: function(msgObj){
                    if(msgObj.ret == 1)
                        window.location.href = '';
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

    $('.attr_add').on('click', function(){
            $('#attr_add').fadeIn();
            $('#attr_name').focus();
    })

    $('#attr_add_a').on('click', function(){
        var id=$('#pattr_id').val(), name=$('#attr_name').val(), rank = $('#attr_rank').val();
        if(!name){
            yunduoErrorShow({'name':{'msg':"属性名不能为空"}});
            return false;
        }
        if(!/^\d+$/.test(rank)){
            yunduoErrorShow({'name':{'msg':"错误的排序,请输入整数,值越大,排行越靠前"}});
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/attributes/addattr",
            data: {aid:id, name:name, rank:rank},
            dataType:'json',
            success: function(msgObj){
                if(msgObj.ret == 1)
                    location.href = "";
                else
                    yunduoErrorShow(msgObj.msg);
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        });
    })

});