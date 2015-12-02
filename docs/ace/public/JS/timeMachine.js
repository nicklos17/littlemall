/******** Base on jquery ********/

// Common Funcion

// 比较两数组
function timeMachine_equal(arrayA,arrayB) {
    if (!arrayA || !arrayB){ return false; } 
    if (arrayA.length != arrayB.length){ return false; }
    for (var i = 0, l = arrayA.length; i < l; i++) {
        if (arrayA[i] != arrayB[i]) { return false; }           
    }
    return true;
}   

// 左补零
function timeMachine_padLeft(str,lenght){ 
    if(str.length >= lenght){
        return str; 
    }else{ 
        return timeMachine_padLeft("0" +str,lenght);
    }
}

/********************************/

// Origin data
var wmd_tm_result = '';    // post 用的数据

var wmd_tm_map = {
	0 : "周一",
	1 : "周二",
	2 : "周三",
	3 : "周四",
	4 : "周五",
	5 : "周六",
	6 : "周日",
};

var t_opear={
  'up':function(obj){
    // 上移
    var base = $(obj).parent().parent();
    var i = base.index();

    if(i>1){

      var tem0_rank = base.find('.rank').eq(0).val();
      var tem1_rank = base.prev().find('.rank').eq(0).val();

      base.find('.rank').eq(0).attr('value',tem1_rank);
      base.prev().find('.rank').eq(0).attr('value',tem0_rank);

      var tem0 = base.html();
      var tem1 = base.prev().html();

      base.prev().html(tem0);
      base.html(tem1);

    }
  },
  'down':function(obj){
    // 下移
    var l = $("#wmd_user_times tr").length;
    var base = $(obj).parent().parent();
    var i = base.index();

    if(i<l-2){

      var tem0_rank = base.find('.rank').eq(0).val();
      var tem1_rank = base.next().find('.rank').eq(0).val();
  
      base.find('.rank').eq(0).attr('value',tem1_rank);
      base.next().find('.rank').eq(0).attr('value',tem0_rank);

      var tem0 = base.html();
      var tem1 = base.next().html();

      base.next().html(tem0);
      base.html(tem1);

    }
  },
  'del':function(obj){
    // 删除
    var id = $(obj).parent().parent().data('tid');
    $(obj).parent().parent().remove();
    if(id != "0"){
      var tmp = $('#times_del_input').val();
      if(tmp == ''){
        tmp = id;
      }else{
        tmp += ',';
        tmp += id;
      }
      $('#times_del_input').val(tmp);
    }
  }
};

/********************************/

// 绑定事件
$(document).on('blur','#wmd_user_times .time_name,#wmd_user_times .time_padding',function(){
    var temp=$(this).val();
    $(this).attr('value',temp);
});
$(document).on('click','input[type="checkbox"]',function(){
    if ($(this).is(':checked')) {
    	$(this).attr('checked', 'checked');
    }else{
    	$(this).removeAttr('checked');
    }
});
$(document).on("click",".tup",function(){
    t_opear.up(this);
});
$(document).on("click",".tdown",function(){
    t_opear.down(this);
});
$(document).on("click",".tdel",function(){
     t_opear.del(this);
});
$(document).on("blur", '.time_padding', function(event) { 
    var str = $(this).val();
    var patt = /^\s*\d{1,2}\s*\:\s*\d{1,2}\s*\-\s*\d{1,2}\s*\:\s*\d{1,2}\s*$/g;
    var res = patt.test(str);
    if(!res){
    	alert('请输入正确的时间格式');
    } 
});

/************* Main **************/

// 生成公告牌 (展示用)
function timeMachine_initBoard(data){

	var res = '';
	var tmp = '';
	var len = 0;
	var now_at = 0;
	var filter = [ [] , [] , [] , [] , [] , [] , [] ];

	for (var k in data) {

		len = data[k].length;

		if( len ){

		    if( k > 0 ){
		        prev_len = data[k-1].length;
		        if( prev_len && timeMachine_equal( data[k-1] , data[k] ) ){

					filter[now_at].push(parseInt(k));
		        }else{

		        	now_at++;
		        	filter[now_at].push(parseInt(k));
		        }
		    }else{

		    	filter[now_at].push(parseInt(k));
		    }

		}
	}

	for (var k in filter) {
		if(filter[k].length){

			if(filter[k].length > 1){
			    var index = filter[k][0];
			    var end = filter[k].length - 1;
			    end   = filter[k][end];	
			    res += '<div class="wmd_board_week"><div>'+wmd_tm_map[index]+' - '+wmd_tm_map[end]+'</div>';
			}else{
				var index = filter[k][0];
				res += '<div class="wmd_board_week"><div>'+wmd_tm_map[index]+'</div>';
			}

		    tmp = '';
		    len = data[index].length;
		    for (var p in data[index]) {
		    	tmp += data[index][p];
		    	if(p != len - 1){
		    		tmp += ' , ';
		    	}
		    }

		    res += '<div class="wmd_board_pad">'+tmp+'</div>';
		    res += '<div class="wmd_clearbox"></div></div>';
		}
	}

	// console.log(filter);

	$('#wmd_timeboard').html(res);
	$('#wmd_timeboard').addClass('wmd_timeboard');
}

function timeMachine_init_for_new(noedit){

	if(!noedit){ noedit = false; }

	/************ build basic html ************/
	// header
	$('<div id="wmd_tm_overlay" style="display: none;"></div>').insertAfter( "#wmd_tm_data" );
    $('<div id="wmd_user_times" style="display: none;"><table class="table table-striped table-bordered table-hover" style="width:100%"><tbody></tbody></table></div>').insertAfter( "#wmd_tm_overlay" );
	if( !noedit ){
	  $('#wmd_user_times tbody').append('<tr><th width="80px;">操作</th><th>时间段</th><th>周一</th><th>周二</th><th>周三</th><th>周四</th><th>周五</th><th>周六</th><th>周日</th></tr>');
	}else{
	  $('#wmd_user_times tbody').append('<tr><th>时间段</th><th>周一</th><th>周二</th><th>周三</th><th>周四</th><th>周五</th><th>周六</th><th>周日</th></tr>');		
	}
	// some btn
	var custom_btn = '<a id="wmd_tm_custom" href="javascript:;"><button class="btn-mini btn" type="button"><i class="icon-plus bigger-80"></i>添加时间段</button></a> ';
	var ok_btn     = '<a id="wmd_tm_cancel" href="javascript:;"><button class="btn-mini btn" type="button">取消</button></a> ';
	var cancel_btn = '<a id="wmd_tm_ok" href="javascript:;"><button class="btn-mini btn" type="button">确定</button></a>';
	if( !noedit ){
	  $('#wmd_user_times tbody').append('<tr class="last"><td colspan="9"><div><input name="times_del_input" id="times_del_input" style="display:none;" type="text" />'+custom_btn+ok_btn+cancel_btn+'</div></td></tr>');
	}else{	
	  $('#wmd_user_times tbody').append('<tr class="last"><td colspan="9"><div><input name="times_del_input" id="times_del_input" style="display:none;" type="text" /><span style="color:gray;font-size:13px;"> 时间段在 "店铺设置" 中进行编辑 </span>'+ok_btn+cancel_btn+'</div></td></tr>');
	}
	/************ build basic html ************/

	var data = $('#wmd_tm_data').val();

	if(data != ''){
	    var init_obj = {
	    	0:[],1:[],2:[],3:[],4:[],5:[],6:[]
	    };
		data = jQuery.parseJSON(data);
		var ceil = '';
	    var res  = '';
	    for (var k in data) {
	    	
	    	var start_hour = timeMachine_padLeft( (( data[k]['start'] / 60 ) | 0).toString() , 2 );
	    	var start_min  = timeMachine_padLeft( (data[k]['start'] % 60).toString()         , 2 );
	    	var end_hour   = timeMachine_padLeft( (( data[k]['end'] / 60 ) | 0).toString()   , 2 );
	    	var end_min    = timeMachine_padLeft( (data[k]['end'] % 60).toString()           , 2 );
	    	var title      = data[k]['title'];
	    	var tid        = data[k]['tid'];
	    	var rank       = data[k]['rank'];
	    	var padding    = start_hour+':'+start_min+'-'+end_hour+':'+end_min;

	    	ceil = '<tr class="wmd_tm_sub_result" data-tid="'+tid+'">';
	    	if( !noedit ){
	    	  ceil += '<td><button class="tdel btn btn-minier btn-light" type="button"><i class="icon-trash bigger-80"></i></button><button class="tup btn btn-minier btn-light" type="button"><i class="icon-chevron-up bigger-80"></i></button><button class="tdown btn btn-minier btn-light" type="button"><i class="icon-chevron-down bigger-80"></i></button></td>';
	          ceil += '<td><input style="width:20%;margin-right: 5px;" type="text" value="'+title+'" placeholder="时间段名" class="time_name" /><input style="width:60%" type="text" value="'+padding+'" class="time_padding" /></td>';
	    	}else{
	          ceil += '<td><input readonly="readonly" style="width:20%;margin-right: 5px;" type="text" value="'+title+'" placeholder="时间段名" class="time_name" /><input readonly="readonly" style="width:60%" type="text" value="'+padding+'" class="time_padding" /></td>';
	    	}

	    	// week	
        
	    	/* Mon */
	    	if( parseInt( data[k]['mon'] ) ){
	    		res = 'checked';
	    		init_obj[0].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_mon" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Tue */
	    	if( parseInt( data[k]['tue'] ) ){
	    		res = 'checked';
	    		init_obj[1].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_tue" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Wed */
	    	if( parseInt( data[k]['wed'] ) ){
	    		res = 'checked';
	    		init_obj[2].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_wed" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Thu */
	    	if( parseInt( data[k]['thu'] ) ){
	    		res = 'checked';
	    		init_obj[3].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_thu" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Fri */
	    	if( parseInt( data[k]['fri'] ) ){
	    		res = 'checked';
	    		init_obj[4].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_fri" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Sat */
	    	if( parseInt( data[k]['sat'] ) ){
	    		res = 'checked';
	    		init_obj[5].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_sat" type="checkbox" value="" '+res+' /><span class="lbl"></span></td>';
        
	    	/* Sun */
	    	if( parseInt( data[k]['sun'] ) ){
	    		res = 'checked';
	    		init_obj[6].push(padding);
	    	}else{
	    		res = '';
	    	}
	    	ceil += '<td><input class="wmd_tm_sun" type="checkbox" value="" '+res+' /><span class="lbl"></span><input style="display:none;" class="rank" value="'+rank+'" /></td>';
	    	// week
        
	    	ceil += '</tr>';
        
            $('#wmd_user_times tbody tr:last').before($(ceil));
	    	// $('#wmd_user_times tbody').append(ceil);
	    	
	    }
        
	    timeMachine_initBoard(init_obj);

	}else{
	
		// 默认值
        html = '<tr class="wmd_tm_sub_result" data-tid="0" >';
        html += '<td><button class="tdel btn btn-minier btn-light" type="button"><i class="icon-trash bigger-80"></i></button><button class="tup btn btn-minier btn-light" type="button"><i class="icon-chevron-up bigger-80"></i></button><button class="tdown btn btn-minier btn-light" type="button"><i class="icon-chevron-down bigger-80"></i></button></td>';
        html += '<td><input style="width:20%;margin-right: 5px;" type="text" value="午餐" placeholder="时间段名" class="time_name" /><input style="width:60%" type="text" value="09:00 - 14:00" class="time_padding" /></td>';
	    html += '<td><input class="wmd_tm_mon" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_tue" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_wed" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_thu" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_fri" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_sat" type="checkbox" value="" checked /><span class="lbl"></span></td>';
        html += '<td><input class="wmd_tm_sun" type="checkbox" value="" checked /><span class="lbl"></span>';
        html += '<input style="display:none;" class="rank" value="1" />';
        html += '</td></tr>';
        html += '<tr class="wmd_tm_sub_result" data-tid="0" >';
        html += '<td><button class="tdel btn btn-minier btn-light" type="button"><i class="icon-trash bigger-80"></i></button><button class="tup btn btn-minier btn-light" type="button"><i class="icon-chevron-up bigger-80"></i></button><button class="tdown btn btn-minier btn-light" type="button"><i class="icon-chevron-down bigger-80"></i></button></td>';
        html += '<td><input style="width:20%;margin-right: 5px;" type="text" value="晚餐" placeholder="时间段名" class="time_name" /><input style="width:60%" type="text" value="16:30 - 21:00" class="time_padding" /></td>';
	    html += '<td><input class="wmd_tm_mon" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_tue" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_wed" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_thu" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_fri" type="checkbox" value="" checked /><span class="lbl"></span></td>';
	    html += '<td><input class="wmd_tm_sat" type="checkbox" value="" checked /><span class="lbl"></span></td>';
        html += '<td><input class="wmd_tm_sun" type="checkbox" value="" checked /><span class="lbl"></span>';
        html += '<input style="display:none;" class="rank" value="2" />';
        html += '</td></tr>';
        $('#wmd_user_times tbody tr:last').before($(html));
    }

    // 新插入行
    $('#wmd_tm_custom').on('click',function(){
    	html = '<tr class="wmd_tm_sub_result" data-tid="0" >';
    	html += '<td><button class="tdel btn btn-minier btn-light" type="button"><i class="icon-trash bigger-80"></i></button><button class="tup btn btn-minier btn-light" type="button"><i class="icon-chevron-up bigger-80"></i></button><button class="tdown btn btn-minier btn-light" type="button"><i class="icon-chevron-down bigger-80"></i></button></td>';
    	html += '<td><input style="width:20%;margin-right: 5px;" type="text" placeholder="时间段名" class="time_name" /><input style="width:60%" type="text" value="00:00 - 24:00" class="time_padding" /></td>';
		html += '<td><input class="wmd_tm_mon" type="checkbox" value="" checked /><span class="lbl"></span></td>';
		html += '<td><input class="wmd_tm_tue" type="checkbox" value="" checked /><span class="lbl"></span></td>';
		html += '<td><input class="wmd_tm_wed" type="checkbox" value="" checked /><span class="lbl"></span></td>';
		html += '<td><input class="wmd_tm_thu" type="checkbox" value="" checked /><span class="lbl"></span></td>';
		html += '<td><input class="wmd_tm_fri" type="checkbox" value="" checked /><span class="lbl"></span></td>';
		html += '<td><input class="wmd_tm_sat" type="checkbox" value="" checked /><span class="lbl"></span></td>';
        html += '<td><input class="wmd_tm_sun" type="checkbox" value="" checked /><span class="lbl"></span>';
        var tmp = 0;
        $("#wmd_user_times .rank").each(function(){
        	if( parseInt($(this).val()) > tmp ){
        		tmp = parseInt($(this).val());
        	}
        });
        var max = tmp + 1;
        html += '<input style="display:none;" class="rank" value="'+max+'" />';
        html += '</td></tr>';
    	$('#wmd_user_times tbody tr:last').before($(html));
    });

	// bind event
    $('#wmd_tm_ok').on('click',function(){

    	// flush board
	    var init_obj = {
	    	0:[],1:[],2:[],3:[],4:[],5:[],6:[]
	    };

    	// generate result
    	wmd_tm_result = '';
    	var len = $('.wmd_tm_sub_result').length;
    	$('.wmd_tm_sub_result').each(function( index ){

    		var tid     = $(this).data('tid');
    		var title   = $(this).find('.time_name').eq(0).val();
    		var padding = $(this).find('.time_padding').eq(0).val();
    		var rank    = $(this).find('.rank').eq(0).val();

    		var tmp = '{';
    		tmp += ' "tid": '+tid+',';
    		tmp += ' "title":"'+title+'",';
    		tmp += ' "padding":"'+padding+'",';
    		tmp += ' "rank":"'+rank+'",';
    		tmp += ' "week": [ ';

    		var tmp_res = '';
    		tmp_res = $(this).find('.wmd_tm_mon').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[0].push(padding);
    		}
    		tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_tue').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[1].push(padding);
    		}
			tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_wed').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[2].push(padding);
    		}
    		tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_thu').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[3].push(padding);
    		}
    		tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_fri').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[4].push(padding);
    		}
    		tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_sat').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[5].push(padding);
    		}
    		tmp += ',';
    		tmp_res = $(this).find('.wmd_tm_sun').eq(0).is(':checked') ? '1' : '0';
    		tmp += tmp_res;
    		if( parseInt( tmp_res ) ){
    			init_obj[6].push(padding);
    		}
    		tmp += ' ] ';

            if (index != len - 1) {
                tmp += ' } , ';
            }else{
            	tmp += ' } ';
            }
    		
    		wmd_tm_result += tmp;

    	});
        timeMachine_initBoard(init_obj);
    	$('#wmd_tm_result').val('');
    	$('#wmd_tm_result').val(' [ ' + wmd_tm_result + ' ] ');
    	// console.log(' [ ' + wmd_tm_result + ' ] ');


    	$('#wmd_user_times,#wmd_tm_overlay').hide();
    });

	$("#wmd_tm_ok").trigger("click");

    $('#wmd_tm_overlay,#wmd_tm_cancel').on('click',function(){
    	$('#wmd_user_times,#wmd_tm_overlay').hide();
    });
}

function timeMachine_show(){

	$('#wmd_user_times,#wmd_tm_overlay').toggle();
}