define(function(require){
    var chkAll = require('chkAll');
    chkAll.run($('#chk-all'), $('.chk-item'));

    $('#listCoupons').on('click', function(){
    var chkDelArr = $('.chk-item:checked'),
    len = chkDelArr.length,
    id = '';
    for(var i=0; i<len; i++){
        id += chkDelArr.eq(i).val() + ',';
    }
    var status = $("#set-select").find("option:selected").val();
    if(id != '')
    {
         $.ajax({
                 type: "POST",
                 url: "/coupons/editeCouponsStatus",
                 data: "id="+id+"&status="+status,
                 dataType: "json",
                 success: function(msg){
                        if(msg.ret == '1'){
                            location.href = ' ';
                        }
                    }
          });
    }
    });

    $('#listCouponsRule').on('click', function(){
    var chkDelArr = $('.chk-item:checked'),
    len = chkDelArr.length,
    id = '';
    for(var i=0; i<len; i++){
        id += chkDelArr.eq(i).val() + ',';
    }
    var status = $("#set-select").find("option:selected").val();
    if(id != '')
    {
         $.ajax({
                 type: "POST",
                 url: "/coupons/editeCouponsRulesStatus",
                 data: "id="+id+"&status="+status,
                 dataType: "json",
                 success: function(msg){
                        if(msg.ret == '1'){
                            location.href = ' ';
                        }
                    }
          });
    }
    });
});