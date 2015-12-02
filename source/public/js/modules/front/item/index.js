define(function(){
    function setc(name, value){
        exp = new Date();
        exp.setTime(exp.getTime() + 86400000);
        document.cookie = name + " = "+ escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    }
    var filter = '';
    //尺码对照表
    $("#shoes-size").click(function(event){
        $(this).find("#shoes-size-table").show();
    });

    $("#tb-close").on("click", function(event){
        $("#shoes-size-table").hide();
        event.stopPropagation();
    });

    // 尺码切换
    $(".shoes-size-id").on("click",function(){
      if($(this).hasClass('disabled'))
          return false;
      if(filter == $(this).data('id'))
          return false;
      else
        filter = $(this).data('id');
      $("#shoes-kinds > .shoes-colors").removeClass('disabled');
      $(this).addClass("size-cur").siblings().removeClass("size-cur");

      //判断商品属性是否选全
      if($(".selected").has("nodata") && $('#shoes-kinds>.color-cur').length === 1 && $('#size-kinds>.size-cur').length === 1)
          $('#select-closed').trigger('click');

      //过滤组合属性库存为空
        $.ajax({
            type: "POST",
            url: "/goods/filterSize",
            timeout : 10000,
            data:{'gid':window.location.pathname.split('/').pop(), 'filter-id':$(this).data('id'),
            'parent-other-id':$('#color').data('id')},
            dataType: "json",
            success: function(msgObj){
              if(msgObj)
             {
                var len = msgObj.length, i = 0;
                for(i; i < len; i ++)
               {
                  $('#shoes-' + msgObj[i]).addClass('disabled');
                }
              }
              return false;
            },
            error:function(){
                window.location.href = '';
            }
        })
    });

    // 颜色切换
    $("#shoes-kinds > .shoes-colors").on("click",function(){
      if($(this).hasClass('disabled'))
          return false;
      if(filter == $(this).data('id'))
          return false;
      else
        filter = $(this).data('id');
      $(".shoes-size-id").removeClass('disabled');
      $(this).addClass("color-cur").siblings().removeClass("color-cur");
      if($(this).attr('src'))
     {
       if($(this).attr('src').indexOf("thumb-shoes")>=0){
        var imgSrc = ($(this).attr('src')).replace(/thumb-/, '');       
         }else{
        var imgSrc = ($(this).attr('src')).replace(/attr-([^-]*)/, 'attr-original');
         }
        switchThumbNav(imgSrc);
      }

      //判断商品属性是否选全
      if($(".selected").has("nodata") && $('#shoes-kinds>.color-cur').length === 1 && $('#size-kinds>.size-cur').length === 1)
          $('#select-closed').trigger('click');
      //过滤组合属性库存为空
        $.ajax({
            type: "POST",
            url: "/goods/filterCol",
            timeout : 10000,
            data:{'gid':window.location.pathname.split('/').pop(), 'filter-id':$(this).data('id'),
            'parent-other-id':$('#size').data('id')},
            dataType: "json",
            success: function(msgObj){
              if(msgObj)
             {
                var len = msgObj.length, i = 0;
                for(i; i < len; i ++)
               {
                  $('#size-' + msgObj[i]).addClass('disabled');
                }
              }
              return false;
            },
            error:function(){
                window.location.href = '';
            }
        })
    });

    // 图片切换函数(无索引)
    function switchThumbNav(src){
        $('.shoes-slide img').attr('src', src);
    }

    // 图片切换函数（带索引）
    function switchThumbNavIndex(src, index){
        $('.shoes-slide img').attr('src', src);
        $('.shoes-thumb').removeClass('cur').eq(index).addClass('cur');
    }

    // 缩略图切换
    $('.shoes-thumb').on('click', function(){
        var $ele = $(this), index = $('.shoes-thumb').index($ele);
        if($ele.find('img').attr('src').indexOf("thumb-shoes")>=0){
            imgSrc = ($ele.find('img').attr('src')).replace(/thumb-/, '');
        }else{
            imgSrc = ($ele.find('img').attr('src')).replace(/item-([^-]*)/, 'item-original');
        }
      $('.shoes-slide img').attr('src', imgSrc);
      $ele.addClass('cur');
      $ele.parent().siblings().find('a').removeClass('cur');
      switchThumbNav(imgSrc);
    });

    if($('#shoes-kinds > .shoes-colors').length == 1)
      $('#shoes-kinds > img').eq(0).trigger('click');
    else
      $('.shoes-thumb').eq(0).trigger('click');


    // 上一张图片
    $('#shoes-prev').on('click', function(e){
      e.preventDefault();
      var $cur = $('.shoes-thumb.cur'), index = $('.shoes-thumb').index($cur) - 1,
            thumb = $($('.slide-nav>li').eq(index)[0]).find('.shoes-thumb>img').attr('src');
      if(thumb.indexOf("thumb-shoes")>=0){
          var src = thumb.replace(/thumb-/, '');
      }else{
        var src = thumb.replace(/item-([^-]*)/, 'item-original');
      }
      if(index == -1){
        index = 4;
      }
      switchThumbNavIndex(src, index);
      return false;
    });

    // 下一张图片
    $('#shoes-next').on('click', function(e){
      e.preventDefault();
      var $cur = $('.shoes-thumb.cur'), index = $('.shoes-thumb').index($cur)+1;
      if(index ==5)
          index = 0;
      thumb = $($('.slide-nav>li').eq(index)[0]).find('.shoes-thumb>img').attr('src');
      if(thumb.indexOf("thumb-shoes")>=0){
          var src = thumb.replace(/thumb-/, '');
      }else{
        var src = thumb.replace(/item-([^-]*)/, 'item-original');
      }
      switchThumbNavIndex(src, index);
      return false;
    });

    // 详情介绍和产品参数切换
    $("#detail-intro").click(function(){
        $(this).addClass("cur").siblings().removeClass("cur");
        $(".detail-introduce").show();
        $(".product-prameter").hide();
    });

    $("#product-pram").click(function(){
        $(this).addClass("cur").siblings().removeClass("cur");
        $(".product-prameter").show();
        $(".detail-introduce").hide();
    });

    $("#num-add").on("click",function(){
      // 商品购买数量更改
      var inputNum = $(".input-num").attr("value"), numChange = parseInt(inputNum);
      if(1 <= numChange &&  numChange < 20 ){//99表示剩余数量
          $(this).removeClass("disabled");
          $("#num-reduce").removeClass("disabled").addClass("pressed");
          numChange = numChange + 1;
          $(".input-num").attr("value",numChange);
        }
        else if (numChange >= 20 ){
          $(this).addClass("disabled").removeClass("pressed");
        }else{
        }

    });

    $("#num-reduce").on("click",function(){
      // 商品购买数量更改
      var inputNum = $(".input-num").attr("value"), numChange = parseInt(inputNum);
      if ( 1 <  numChange &&  numChange <= 20){
          $(this).removeClass("disabled").addClass("pressed");
          $("#num-add").removeClass("disabled").addClass("pressed");
          numChange = numChange - 1;
          $(".input-num").attr("value",numChange);
        }
        else if ( numChange <= 1 ){
            $(this).removeClass("pressed").addClass("disabled");
        }
    });

    // 商城购买信息不全时的下一步提示
    $('#btn-add-cart').on('click', function(){

        var $color = $('#shoes-kinds>.color-cur'), $size = $('#size-kinds>.size-cur');
        if($color.length !== 1 || $size.length !== 1)
        {
            $(".selected").addClass("nodata");
            $("dt:first,dd:first").hide();
            $(".unselected").show();
            $(".shoes-btn-next").show().siblings().hide();
            return false;
        }
          //加入购物车
          $.ajax({
            type: "POST",
            url: "/cart/add",
            timeout : 10000,
            data:{'gid':window.location.pathname.split('/').pop(), 'attrs-ids':$color.data('id') + ',' + $size.data('id'),
            'num':$('#input-num').val()},
            dataType: "json",
            success: function(msgObj){
               if(msgObj.ret == 1)
                  window.location.href = '/cart';
              else if(msgObj.ret == 3)
                  window.location.href = '/public/errorAjax?v=7';
              else if(msgObj.ret == 5)
                  window.location.href = '/public/errorAjax?v=8';
              else
                  window.location.href = '/public/errorAjax?v=1';
              return false;
            },
            error:function(){
                var goodsUrl = window.location.protocol + '//' + window.location.host + '/goods/index/' + window.location.pathname.split('/').pop();
                window.location.href = udomain + '/login?siteid=3&backurl=' + encodeURIComponent(goodsUrl);
            }
        })
    })

    $('#btn-soon-buy').on('click', function(){

        if($('#shoes-kinds>.color-cur').length !== 1 || $('#size-kinds>.size-cur').length !== 1)
        {
            $(".selected").addClass("nodata");
            $("dt:first,dd:first").hide();
            $(".unselected").show();
            $(".shoes-btn-next").show().siblings().hide();
            return false;
        }
        var num = $('#input-num').val();
        if(!/^[1-9][0-9]*$/.test(num))
            return false;
        if(num > 20){
          window.location.href = '/public/errorAjax?v=8';
          return false;
        }
        cv = '{"g":"'
        + window.location.pathname.split('/').pop()
        + '", "a":"'
        + $('#shoes-kinds>.color-cur').data('id')
        + ','
        + $('#size-kinds>.size-cur').data('id')
        + '", "n":"'
        + num
        + '"}';
        setc('cg', cv);
        window.location.href = '/order/confirm/';
    })

    $('#select-closed').on("click",function(){
        $(".selected").removeClass("nodata");
        $("dt:first,dd:first,.unselected").show();
        $(".unselected").hide();
        $(".shoes-btn-next").hide().siblings().show();
        event.stopPropagation();
    });
});
