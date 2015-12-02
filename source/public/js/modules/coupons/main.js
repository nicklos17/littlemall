define(function(require){
    $('body').on('change','.rangeType',function(){
        var rangeType = $(this).val();
        if(rangeType =='1'){
            $(".range").hide();
        }else if(rangeType == '5'){//按分类
            $(".goodname").hide();
            $(".catname").show();
            var url_ = "/coupons/getCategory";
            $("#catname").bigAutocomplete({url:url_,callback:function(data){
                  $('#catid').val(data.result.id);
               }
           }); 
        }else if(rangeType == '3'){//按商品
            $(".catname").hide();
            $(".goodname").show();
            var url_ = "/coupons/getGoodsName";
            $("#goodname").bigAutocomplete({url:url_,callback:function(data){
                  $('#gid').val(data.result.id);
               }
           }); 
        }
    });

        $('.coupons_edit').on('click',function(){
        var couponsObj = $(this).parent().parent();
        var id = $(this).attr('data');
        var status = couponsObj.find('.cp_status').val();
        var confirm = '<a href="javascript:void(0);"  class="btn btn-mini coupons-confirm" data="'+id+'">确定</a><a href="javascript:void(0);"  class="btn btn-mini coupons-cancel">取消</a>';
        if(status == '1'){
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' selected='selected'>可用</option><option value='3'>禁用</option></select>";
        } else if (status == '3'){
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' >可用</option><option value='3' selected='selected'>禁用</option></select>";
        }
       couponsObj.find('#cp_status').html(statusHtml);
        $(this).parent().html(confirm);
    });
    //取消
    $('body').on('click','.coupons-cancel',function(){
        location.href = '';
    });
    //确认修改状态
    $('body').on('click','.coupons-confirm',function(){
        var couponsObj = $(this).parent().parent();
        var id = couponsObj.find('.cpid').val();
        var status = couponsObj.find("option:selected").val();
         $.ajax({
                 type: "POST",
                 url: "/coupons/editeCouponsStatus",
                 data: "id="+id+"&status="+status,
                 success: function(msg){
                    if(msg == 1){
                        location.href = ' ';
                    }
                }
          });
    });

      //规则
    $('.couponsRules_edit').on('click',function(){
        var couponsObj = $(this).parent().parent();
        var id = $(this).attr('data');
        var status = couponsObj.find('.cr_status').val();
        var confirm = '<a href="javascript:void(0);"  class="btn btn-mini couponsRules-confirm" data="'+id+'">确定</a><a href="javascript:void(0);"  class="btn btn-mini couponsRules-cancel">取消</a>';
        if (status == '1') {
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' selected='selected'>可用</option><option value='3'>禁用</option></select>";
        } else if(status == '3') {
            var statusHtml = "<select id='form-search-field-select' style='width: 110px;'' name='status'><option value='1' >可用</option><option value='3' selected='selected'>禁用</option></select>";
        }
        couponsObj.find('#cr_status').html(statusHtml);
        $(this).parent().html(confirm);
    });
    //取消
    $('body').on('click','.couponsRules-cancel',function(){
        location.href = '';
    });
    //确认修改状态
    $('body').on('click','.couponsRules-confirm',function(){
        var couponsObj = $(this).parent().parent();
        var id = couponsObj.find('.crid').val();
        var status = couponsObj.find("option:selected").val();
         $.ajax({
                 type: "POST",
                 url: "/coupons/editeCouponsRulesStatus",
                 data: "id="+id+"&status="+status,
                 success: function(msg){
                        if(msg == 1){
                            location.href = ' ';
                        }
                    }
          });
    });
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
    $('#form-search-clear').on('click', function(){
    $('#form-search-input').val('');
    });

    function trim(str)
    {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }
});