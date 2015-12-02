// 显示一个弹出框
// src 弹出框内嵌的iframe网址
// title 标题
// width,height 宽和高
// callback 关闭按钮回调函数
// title 标题，src内嵌iframe,width 宽度，height 高度
// callback 弹框后的回调函数
// id 弹框id
function showDialog(title, src, width, height, clickCallback, hideCallback, id) {
    if (!arguments[0]) title = null;
    if (!arguments[1]) src = null;
    if (!arguments[2]) width = 500;
    if (!arguments[3]) height = 200;
    if (!arguments[4]) clickCallback = null;
    if (!arguments[5]) hideCallback = null;
    if (!arguments[6]) id = '1';

    // 判断是否存在id
    // 判断id所对应弹框是否存在，如果不存在则创建，存在则删除

    var id = 'waimaid-modal-' + id;
    var m = null;
    if ($("#" + id).length > 0) {
        m = $("#" + id);
    } else {
        m = $("#waimaid-modal-template").clone(true);
        m.attr("id", id);
        $("body").append(m);
    }

    // 设置宽、高
    m.css("width", width);
    m.css("margin-left", -width / 2);
   //height = Math.min(height, $(window).height() * .8);
    m.height(Math.height);

    // 如果标题存在则设置标题
    if (title != null) {
        m.find(".modal_title").html(title);
    }

    if (src != null) {
        var subframe = m.find(".model_iframe");
        subframe.attr('src', src);
        subframe.css('height', height - 110);
    }

    var okbtn = m.find(".modal_btn_ok");
    okbtn.unbind("click").click(function () {
        if (src != null) {
            subframe[0].contentWindow.parentOkBtnClick(function (data) {
                clickCallback(data, m);
            });
        }
        else {
            clickCallback(null, m);
        }
    });

    m.modal({
        backdrop: false,
        keyboard: true,
        show: true
    });

    if (hideCallback != null) {
        m.unbind('hide.bs.modal').on('hide.bs.modal', hideCallback);
    }
}

//显示一个手机预览
function showMobile(title,url){
	var src = '/Home/Wx/mobile?url='+encodeURIComponent(url);
	showDialog(
		title,  // 标题
		src,    // iframe链接
        550, 	// 对话框宽和高
        950,
        function (res, modal) {
            modal.modal('hide');
        }
    );
}

/**
 * 自动生成搜索使用框
 * searchDiv 放置的div对象，会在内部生成搜索框
 * options 选择数组  array ("key"=>"keyName")
 * defaultKey  默认可以为空
 * defaultValue  默认搜索值
 * 查询使用链接，会自动带上 key/searchKey/value/searchValue 参数
 */
 function creatFormSearch(searchDiv,options,defaultKey,defaultValue,searchUrl){
	var optionHtml = '';
	for(var okey in options){
		optionHtml += '<option value="'+okey+'">'+options[okey]+'</option>';
	}
	searchDiv.html(
			'<div class="form-search">'+
			'<select id="form-search-field-select" style="width: 110px;">'+
				optionHtml+
	       '</select>'+
	       '<span class="input-icon">'+
	            '<input type="text" style="width: 120px;margin:0 0 0 5px;" autocomplete="off" id="form-search-input"'+
	                'class="input-small nav-form-search-input" placeholder="搜索..." value="'+defaultValue+'">'+
	            '<i style="padding-left:8px;" class="icon-search nav-search-icon"></i>'+
	            '<i id="form-search-clear" class="icon-remove nav-remove-icon" style="display:none;left:auto;right:3px;cursor:pointer;"></i>'+
	        '</span>'+
	        '<button class="btn btn-small" type="button" id="btn-search">搜索</button></div>'
    );
	
	if(defaultKey != ''){
		searchDiv.find("#form-search-field-select").val(defaultKey);
	}
	
	if(searchDiv.find("#form-search-input").val() != ''){
		searchDiv.find('#form-search-clear').show();
	}
	
	searchDiv.find("#form-search-input").focus();
	
	searchDiv.find('#btn-search').on('click',searchClick);
	
	function searchClick(){
		var key   = searchDiv.find("#form-search-field-select").val();
		var value = searchDiv.find("#form-search-input").val();
		if (searchUrl.indexOf(searchUrl.length-1, searchUrl.length) != "/") {
			searchUrl = searchUrl + "/";
		}
		window.location.href = searchUrl + "key/" + encodeURIComponent(key) + "/value/" + encodeURIComponent(value);
	}

	searchDiv.find('#form-search-input').on('keyup click',function(e){
		if(e.keyCode == 13){
			searchClick();
			return;
		}
		
		if($(this).val() != ''){
			searchDiv.find('#form-search-clear').show();
		}else{
			searchDiv.find('#form-search-clear').hide();
		}
	});

	searchDiv.find('#form-search-clear').on('click',function(){
		searchDiv.find('#form-search-input').val('');
		searchDiv.find('#form-search-clear').hide();
		searchClick();
	});
}