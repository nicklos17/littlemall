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
                '<input type = "text" class = "gac-attr" value="'+name+'" style = "width:50px;" />'
                + '<input type = "hidden" class = "gac-attr-n" name = "gac_attr[]" value = "'+id+','+pId+',\''+name+'\'" style = "width:100px" />'
            );
        }else{
            delete attributes[pId][id];
            $(this).next('span').html(name);
        }
    });

    $('body').on('blur', '.gac-attr' ,function(){
        var $attr = $(this).parent().parent().parent().parent().prev(), pName = $attr.data('name'),  pId = $attr.data('id')
        id = $(this).parent().parent().data('id'),name = $(this).val();
        attributes[pId][id] = name;
        $(this).parent().html('<input type = "text" class = "gac-attr" value="'+name+'" style = "width:50px;" />'
        +'<input type = "hidden" class = "gac-attr-n" name = "gac_attr[]" value = "'+id+','+pId+',\''+name+'\'" style = "width:100px" />');
    })

    $('#attr-generate').on('click', function(){
        var html = '<table class="gridtable">'
        +'<th>颜色/尺码</th>'
        +'<th>条形码</th>'
        +'<th>库存数量</th>'
        +'<th>是否可用</th>'
        +'<th>可卖数</th><tbody>', 
        arrSize = [], arrColor = [];
        //暂时只处理尺码和颜色的排列组合．后续需要其他属性此模块重新处理
        for (var i in attributes){
            for (var j in attributes[i]){
                if(j !== 'name'){
                    if(attributes[i]['name'].indexOf('尺')===0)
                        arrSize[j] = attributes[i][j];
                    else
                        arrColor[j] = attributes[i][j];
                }
            }
        }
        for( var m in arrColor){
            for( var j in arrSize){
                html+="<tr><td>"+arrColor[m]+"/"+arrSize[j]+"<input type=\"hidden\" name = \"attr_ids[]\" value = "+m+","+j+"></td>"
                +"<td><input type=\"text\" name = \"g_attr_barcode[]\" class=\"required\" value = '' placeHolder = \"请输入条形码\"/></td>"
                +"<td><input type=\"text\"  class=\"required digits\" value = '' name = \"g_attr_stocks[]\" placeHolder = \"库存数量\"/></td>"
                +"<td><select class=\"attrs_enabled\" name=\"g_attr_enable[]\"><option value='1'>可用</option><option value='3'>不可用</option></select></td>"
                +"<td><input type=\"text\" name = \"g_attr_nums[]\" value = '' placeHolder = \"可卖数\"/></td></tr>";
            }
        }
        html+='<tbody></table>';
        $('#goog-attr-table').find('.controls').html(html);
        $('#goog-attr-table').show();
        resizeWindow();

    })

    //商品缩略图处理
    $('#thumb-table .add-img').on('click', function(){
        if($("#thumb-table input[type='file']").length == 5)
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

//商品名　商品sn码唯一
    $('#goods-name').on('blur',function(){
        var $name = $(this);
        if($name.val()){
            $.post('/item/existGoodsName',{name:$name.val()},function(msgObj){
                if(msgObj.ret == 1)
                    $('#yunduo-error-msg').empty();
                else{
                    yunduoErrorShow(msgObj.msg);
                    $name.val('');
                }
                return;
            }, 'json');
        }

    });

    $('#goods-sn').on('blur',function(){
        var $sn = $(this);
        if($sn.val()){
            $.post('/item/existGoodsSn',{sn:$sn.val()},function(msgObj){
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
            if(!$("input[name='attr_ids[]']").val()){
                alert('请先组合商品属性表');
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
})
