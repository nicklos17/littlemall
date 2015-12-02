define(function(require){
    // var chkAll = require('chkAll');
    // chkAll.run($('#chk-all'), $('.chk-item'));
    //时间控件
    Calendar.setup({
        weekNumbers: true,
        inputField : "start_uploadtime",
        trigger    : "start_uploadtime",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
    });

    Calendar.setup({
        weekNumbers: true,
        inputField : "end_uploadtime",
        trigger    : "end_uploadtime",
        dateFormat: "%Y-%m-%d %H:%M:%S",
        showTime: true,
        minuteStep: 1,
        onSelect   : function() {this.hide();}
    });

    //编辑属性
    $('.attr_edit').on('click',function(){
        var id = $(this).attr('data');
        var uid =  $('.uid'+id).text();
        var status = $('.status'+id +' input').val();
        var confirm = '<a href="javascript:void(0);"  class="btn btn-mini code-confirm" data="'+id+'">确定</a><a href="javascript:void(0);"  class="btn btn-mini code-cancel">取消</a>';
        if(status == '1'){
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' selected='selected'>可用</option><option value='3'>禁用</option></select>";
        } else if(status == '3') {
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' >可用</option><option value='3' selected='selected'>禁用</option></select>";
        }
        $('.uid'+id).html('<input type="text"  name="uid" class="uid" class="required" value="'+trim(uid)+'" />');
        $('.status'+id).html(statusHtml);
        $(this).parent().html(confirm);
    });

    //取消
    $('body').on('click','.code-cancel',function(){
        location.href = '';
    });

    //确定
    $('body').on('click','.code-confirm',function(){
        var codeobj = $(this).parent().parent();
        var uid = codeobj.find('.uid').val();
        var id = codeobj.find('.ycid').val();
        var status = codeobj.find("option:selected").val();
         $.ajax({
                 type: "POST",
                 url: "/codes/editeCode",
                 data: "uid="+uid+"&id="+id+"&status="+status,
                 success: function(msg){
                        if(msg == '1'){
                            location.href = ' ';
                        }
                    }
          });

    });
    $('#form-search-clear').on('click', function(){
    $('#form-search-input').val('');
    });

    function trim(str){
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }
});