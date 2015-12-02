function yunduoErrorShow(errorMsg){
    var html = '', key;
    for(var key in errorMsg){
        html += '<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">×</a><strong>警告！</strong> '+errorMsg[key]['msg']+'</div>';
    }
    $('#yunduo-error-msg').html(html);
    $('body').animate({scrollTop:0}, 500);
}

var resizeWindow = function(){
        var uri = window.location.href, offset = document.documentElement.scrollHeight;
        if(uri.indexOf('/item/index') != -1){
            offset = 900;
        }
        if(uri.indexOf('/region/index') != -1){
            offset = 1800;
        }
        hashH = offset + 50;
        urlC = window.admPath + '/index/adjustHeight';
        document.getElementById("iframeA").src=urlC+"?f="+(new Date().getTime())+"#"+hashH;
}