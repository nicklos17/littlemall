define(function(require){

     Calendar.setup({
      weekNumbers: true,
        inputField : "start-promote-time",
        trigger    : "start-promote-time",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
      });

    Calendar.setup({
         weekNumbers: true,
        inputField : "end-promote-time",
        trigger    : "end-promote-time",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
      });

    Calendar.setup({
         weekNumbers: true,
        inputField : "start-time",
        trigger    : "start-time",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
      });

    Calendar.setup({
         weekNumbers: true,
        inputField : "end-time",
        trigger    : "end-time",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
      });

    $('#form-search-clear').on('click', function(){
        $('#form-search-input').val('');
    })

//编辑器初始化
    var um = UM.getEditor('good-desc');
    $("input[type='checkbox']").prop('checked', false);

//商品属性模块
    var attributes = {};
    $('.sku_ul li .attr-checked').on('click', function(){
        var $that = $(this).parent(), $attr = $that.parent().parent(), pId = $attr.prev().data('id'),
        pName = $attr.prev().data('name'), id = $that.data('id'), name = $that.attr('data-name');

        if(!attributes[pId] ){
            attributes[pId] = {} ;
            attributes[pId].name = pName;
        }
        if($(this).prop('checked')){
            attributes[pId][id] = name;
            $(this).next('span').html(
                '<input type = "text" class = "gac-attr" value="'+name+'" style = "width:40px;" />' 
                + '<input type = "hidden" class = "gac-attr-n" name = "gac_attr[]" value = "'+id+','+pId+',\''+name+'\'"/>'
                //+ ''
            );
        }else{
            delete attributes[pId][id];
            $(this).next('span').html(name);
        }
    });

    $('body').on('blur', '.gac-attr' ,function(){
        var $attr = $(this).parent().parent().parent().parent().prev(), pName = $attr.data('name'),  pId = $attr.data('id')
        id = $(this).parent().parent().data('id'), name = $(this).val();
        attributes[pId][id] = name;
        $(this).parent().find('.gac-attr').attr('value', name);
        $(this).parent().find('.gac-attr-n').attr('value', id + ',' + pId + ',\'' + name + '\'');
    })

    $('#goog-attr-table').on('click', '.attr-alter' ,function(){
        var gid = $("input[name='goods_id']").val(), $attr = $(this).parent().parent(), attr_ids = $attr.find('.attrs_ids').val(), 
        g_attr_barcode =  $attr.find('.attrs_barcode').val(), g_attr_stocks =  $attr.find('.attrs_stocks').val(), g_attr_enable =  $attr.find('.attrs_enabled').val(), g_attrs_nums =  $attr.find('.attrs_nums').val();

        $.ajax({
            type: "POST",
            url: "/item/attrsAlter",
            data: {goods_id: gid, attr_ids: attr_ids, g_attr_barcode: g_attr_barcode, g_attr_stocks: g_attr_stocks, g_attr_enable: g_attr_enable, g_attrs_nums: g_attrs_nums},
            dataType: "json",
            success: function(msgObj){
                if(msgObj.ret == 1)
                    alert('编辑成功');
                else
                    alert('编辑失败,请重试');
            },
            error:function(){
                alert('响应超时,请重新尝试');
            }
        })
    })

    $('#attr-generate').on('click', function(){
        var html = '<table class="gridtable"><th>颜色/尺码</th><th>条形码</th><th>库存数量</th><th>是否可用</th><th>可卖数</th><tbody>', 
        arrSize = [], arrColor = [];
        //暂时只处理尺码和颜色的排列组合．后续需要其他属性此模块重新处理
        for(var i in attributes){
            for (var j in attributes[i]){
                if(j !== 'name'){
                    if(attributes[i]['name'].indexOf('尺')===0)
                        arrSize[j] = attributes[i][j];
                    else
                        arrColor[j] = attributes[i][j];
                }
            }
        }

        for(var m in arrColor){
            for( var j in arrSize){
                var ids = m+","+j;
                if(gAttrs[ids]){
                    var chEnable = gAttrs[ids].g_attr_enable == 1 ? 'selected' : '', chDisable = gAttrs[ids].g_attr_enable == 3 ? 'selected' : '';
                    html+="<tr><td>"+arrColor[m]+"/"+arrSize[j]+"<input type=\"hidden\" name = \"attr_ids[]\" value = "+ids+"></td>"
                    +"<td><input type=\"text\" name = \"g_attr_barcode[]\"  class=\"required\" value = '"+gAttrs[ids].g_attr_barcode+"' placeHolder = \"请输入条形码\"/></td>"
                    +"<td><input type=\"text\" value = '"+gAttrs[ids].g_attr_stocks+"' name = \"g_attr_stocks[]\"  class=\"required digits\"  placeHolder = \"库存数量\"/></td>"
                    +"<td><select name='g_attr_enable[]'><option value=\"1\" " + chEnable + ">可用</option><option value=\"3\" " + chDisable + ">不可用</option></select></td>"
                    +"<td><input type=\"text\" name = \"g_attr_nums[]\" value = '"+gAttrs[ids].g_attr_nums+"' placeHolder = \"可卖数\"/></td></tr>";
                }else{
                    html+="<tr><td>"+arrColor[m]+"/"+arrSize[j]+"<input type=\"hidden\" name = \"attr_ids[]\" value = "+ids+"></td>"
                    +"<td><input type=\"text\" name = \"g_attr_barcode[]\"  class=\"required\" value = '' placeHolder = \"请输入条形码\"/></td>"
                    +"<td><input type=\"text\" value = '' name = \"g_attr_stocks[]\"  class=\"required digits\"  placeHolder = \"库存数量\"/></td>"
                    +"<td><select name='g_attr_enable[]'><option value=\"1\">可用</option><option value=\"3\">不可用</option></select></td>"
                    +"<td><input type=\"text\" name = \"g_attr_nums[]\" value = '' placeHolder = \"可卖数\"/></td></tr>";
                }
            }
        }
        html+='<tbody></table>';
        $('#goog-attr-table').find('.controls').html(html);
        $('#goog-attr-table').show();
        resizeWindow();
    })

    for(var i in gaAttrs){
      $('.sku_ul>li').each(function(index){
            if($(this).data('id') == i){
                $(this).attr('data-name', gaAttrs[i]);
                $(this).find('span').html(gaAttrs[i]);
                if(gaAttrsImg[i])
                    $(this).find('.attr-img').html('<img src=' + gaAttrsImg[i] + ' style = "width:40px;">');
                $(this).find("input[type = 'checkbox']").trigger('click');
                return;
            }
      });
    }

        //商品缩略图处理
    $('#thumb-table').on('click', ' .add-img', function(){
        if(($("#thumb-table input[type='file']").length + $('.thumb-ul>li').length) >= 5)
        {
            yunduoErrorShow({'name':{'msg':"只能添加5个缩略图"}});
            return false;
        }
        $(this).parent().parent().parent().append('<tr><td><a href="javascript:void(0)" class="remove-img">&nbsp;[-]</a>'
        +'上传文件 <input type="file" name="goods_pics[]"></td></tr>');
    });

    $('body').on('click', '#thumb-table .remove-img',function(){
        $(this).parent().parent().remove();
    });

    $('.thumb-ul .del-img').on('click', function(){
        var $img = $(this).parent().parent(), goodId = $("input[name='goods_id']").val(), img = $img.find('img').attr('src');
        if(confirm('是否确认删除此图片')){
            $.ajax({
                type: "POST",
                url: "/item/delgoodsimg",
                data: {gid:goodId, img:img},
                dataType: "json",
                success: function(msgObj){
                    if(msgObj.ret == 1){
                        $img.remove();
                        $('#thumb-table>tbody>tr').html('<td><a href="javascript:void(0);" class="add-img">[+]</a>上传文件 <input type="file" name="goods_pics[]"></td>');
                    }
                    else
                        yunduoErrorShow(msgObj.msg);
                },
                error:function(){
                    alert('响应超时,请重新尝试');
                }
            })
        }
    })

//商品名　商品sn码唯一
    // $("#goods-name").on("blur",function(){
    //     var $name = $(this);
    //     if($name.val()){
    //         $.post('/item/existGoodsNameEdit',{name:$name.val(), gid:$("input[name='goods_id']").val()},function(msgObj){
    //             if(msgObj.ret == 1)
    //                 $('#yunduo-error-msg').empty();
    //             else{
    //                 yunduoErrorShow(msgObj.msg);
    //                 $name.val('');
    //             }
    //             return;
    //         }, 'json');
    //     }
    // });

    $("#goods-sn").on("blur",function(){
        var $sn = $(this);
        if($sn.val()){
            $.post('/item/existGoodsSnEdit',{sn:$sn.val(), gid:$("input[name='goods_id']").val()},function(msgObj){
                if(msgObj.ret == 1){
                    $('#yunduo-error-msg').empty();
                }else{
                    yunduoErrorShow(msgObj.msg);
                    $sn.val('');
                }
                return;
            }, 'json');
        }
    });

    //表单验证
    $('#item-form').ajaxForm({
       dataType:'json',
       beforeSubmit:function(){
            if(!$('#item-form').valid()){
                return false;
            }
       },
        error: function(request) {
            alert("Connection error");
        },
        success:function(msgObj){
            if(msgObj.ret == 1)
                window.location.href = '/item/index';
            else
                yunduoErrorShow(msgObj.msg);
            return;
        }
    });

    //商品属性图片处理
    $('.sku_ul .attr-img-edit').on('click', function(){
        $('input[type=file][name=upfile]').remove();
        $(this).parent().parent().append('<input type="file" style="position: absolute; width: 10px; '
        + 'filter: alpha(opacity=0); opacity: 0; top: 0px;" id="upfile" name="upfile" size="20">');
        $('#upfile').trigger('click');
    });

    $('body').on('change', '#upfile', function(){
        var gid = $("input[name='goods_id']").val(), $attr = $(this).parent(), aid = $attr.data('id');
        $.ajaxFileUpload({
            url: '/item/uploadAttrImg?gid='+gid+'&aid='+aid,
            secureuri: false,
            fileElementId: 'upfile',
            dataType: 'json',
            success: function(msgObj){
                if(msgObj.ret != 1)
                    yunduoErrorShow(msgObj.msg);
                else
                    $attr.find('.attr-img').html('<img src=' + msgObj.img + ' style = "width:45px">');
                $('#upfile').remove();
            }
        });
    });
})
