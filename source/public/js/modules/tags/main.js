define(function(require){

    var chkAll = require('chkAll');
    chkAll.run($('#chk-all'), $('.chk-item'));
    $('body').on('click', '.tags-edit',function(){
        var $attr=$(this).parent().parent(), val=$attr.find('.tag-name').html();
        $attr.find('.tag-name').html("<input type='text' value='"+val+"' class='tags-edit-a' name='tag-name' style='width:100px'/>");
        $(this).parent().html('<a href="javascript:void();"  class="btn btn-mini tags-edit-s">确定</a>'+
            '<a href="javascript:void();"  class="btn btn-mini tags-edit-r">取消</a>');
        $(this).remove();
    });

    $('body').on('click', '.tags-edit-s', function(){
        var $callTag = $(this).parent(), $tag = $(this).parent().parent(), tagsId = $tag.find('.tag-id').val(), $name = $tag.find('.tag-name'), name=$tag.find('.tag-name > .tags-edit-a').val();
        if(!name){
            yunduoErrorShow({'name':{'msg':"标签名不能为空"}});
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/tags/edittags",
            data: {tid:tagsId,name:name},
            dataType:'json',
            success: function(msgObj){
                if(msgObj.ret == 1){
                    $name.html(name);
                    $callTag.html('<a href="javascript:void(0)" class="btn btn-mini tags-edit"><i class="icon-pencil"></i></a>'+
                        '<a href="javascript:void(0)" class="btn btn-mini tags-del"><i class="icon-trash"></i></a>');
                    $('#yunduo-error-msg').empty();
                }
                else
                    yunduoErrorShow(msgObj.msg);
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    });

    $('body').on('click', '.tags-edit-r', function(){
        var $tag = $(this).parent(), $ptag = $(this).parent().parent(), $name = $ptag.find('.tag-name'), name=$ptag.find('.tag-name > .tags-edit-a').val();
        $name.html(name);
        $tag.html('<a href="javascript:void(0)" class="btn btn-mini tags-edit"><i class="icon-pencil"></i></a>'
        +'<a href="javascript:void(0)" class="btn btn-mini tags-del"><i class="icon-trash"></i></a>');
    })

    $('body').on('click', '.tags-del', function(){
        if(confirm('确认删除?'))
        {
            var $tag=$(this).parent().parent(), tagsId=$tag.find('.tag-id').val();
            $.ajax({
                type: "POST",
                url: "/tags/deltags",
                data: {tid:tagsId},
                dataType: "json",
                success: function(msgObj){
                    if(msgObj.ret == 1){
                        $tag.remove();
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

    //批量删除
    $('#del-chk').on('click', function(){

        if(confirm('想好了,确定要删除?')){
            var chkDelArr = $('.chk-item:checked'),
                len = chkDelArr.length,
                delIdArr = '';

            for(var i=0; i<len; i++){
                delIdArr += chkDelArr.eq(i).val() + ',';
            }
            if(delIdArr){
                $.ajax({
                    type: "POST",
                    url: "/tags/batchdeltags",
                    data: {tagsIds: delIdArr},
                    dataType: "json",
                    success: function(msgObj){
                        if(msgObj.ret == 1){
                            location.href = '';
                            $('#yunduo-error-msg').empty();
                        }
                        else
                            yunduoErrorShow(msgObj.msg);
                    },
                    error:function(){
                        alert('响应超时,请重新尝试');
                    }
                });
            }
        }
    });

    $('#form-search-clear').on('click', function(){
        location.href = '/tags';
    })

    $('#search').on('click', function(){
        var tagsName = $('#tags-name').val();
        var params = '';
        if(tagsName) params += '&tags_name='+tagsName;
        if(params != ''){
            location.href = '/tags' + params;
        }
    });
})
