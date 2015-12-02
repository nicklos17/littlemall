define(function(){
    $('#cart').unbind('mouseenter').unbind('mouseleave');
    function getc(name){
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        if(arr !=null)
          return unescape(arr[2]);
        return null;
    }

    function delc(name){
        var exp = new Date(), cval=getc(name); 
        exp.setTime(exp.getTime() - 1); 
        if(cval != null) 
          document.cookie = name + "="+cval+";expires="+exp.toGMTString() + ";path=/"; 
    }
  if(!getc('cg'))
      window.location.href = '/cart';
  var cgs = JSON.parse(getc('cg')), $addrBox = $('.address');

  var ajaxPostReq = function(url, data, success){
      $.ajax({
          url:url,
          method:'POST',
          data:data,
          timeout : 10000,
          dataType:'json',
          success:success,
          error:function(){
              window.location.href='';
          }
      })
  }

    if($addrBox.find('.active').data('pro')){
        ajaxPostReq('/order/getShipPay', {proId:$addrBox.find('.active').data('pro')},//初始化快递费用
            function(data){
                if(data.ret == 1){
                    $('#shipping-pay').html(parseFloat(data.data).toFixed(2))
                }else{
                    alert('快递费用加载失败,请刷新后重试')
                }
            }
        )
    }

    $('.addr-order').on('click', function(){
        if(!$(this).hasClass('active')){
            var aid = $(this).data('id')
            ajaxPostReq('/order/getShipPay', {proId:$(this).data('pro')},
                function(data){
                    if(data.ret == 1){
                        $addrBox.find('.active').removeClass('active')
                        $('#address-'+aid).addClass('active')
                        var fee = parseFloat(data.data).toFixed(2), pfee = parseFloat($('#shipping-pay').html()).toFixed(2),
                        $e = $('#eprice');
                        //是否使用云码

                        if(fee != pfee){
                            $('#shipping-pay').html(fee)
                            if(codeFlag == 1){
                              e.html('0.00')
                            }
                            else
                              fee == 0 ? $e.html((parseFloat($e.html())-pfee).toFixed(2)) : $e.html((parseFloat($e.html())+parseFloat(data.data)).toFixed(2));
                        }
                        if($('#ticket').is(':checked') && $('#invoice').val() == 3){
                            $('#invoice-title').val($('.address > .active').find('.address-header > .user-name').html());
                        }
                    }else{
                        alert('快递费用加载失败,请刷新后重试')
                    }
                }
            )
        }
    })
  
  if(typeof cgs.n !== 'undefined'){
      var codeFlag = 0;
      $('#back-cart').hide();
      $.ajax({
          type: "POST",
          url: "/order/buynow",
          data:{'g':cgs.g, 'a':cgs.a, 'n':cgs.n},
          dataType: "json",
          success: function(msgObj){
            if(msgObj.ret == 1){
              var data = msgObj.g, tprice = (data.goods_price*cgs.n).toFixed(2), html = 
                '<tr class="table-data">'
                + '<td class="col-1 col-first">'
                +'<a href="javascript:void(0);" class="order-item-pic dib fl">'
                + '<img style="margin-top:10px" src=" ' 
                + data.goods_img
                + '" alt="'+data.goods_name+'">'
                +'</a>'
                + '<div class="order-item-desc dib">'
                + '<span class="order-item-name">'+data.goods_name+', '+data.g_attr_barcode+'</span>'
                + '<span class="order-item-info">颜色:'+data.col+' 尺码:'+data.size+'</span>'
                + '</div>'
                + '</td>'
                + '<td class="col-2 tac">'
                + data.goods_price
                + '</td>'
                + '<td class="col-3 tac">'
                + cgs.n
                + '</td>'
                + '<td class="col-4 tac">'
                + tprice
                + '</td>'
                + '</tr>';
                $('#goods-list tbody').html(html);
                $('#tprice').html(tprice);
                //使用云码
                if(data.codeFlag == 1){
                    codeFlag = 1;
                    $('#cps-tips').html((parseFloat($('#shipping-pay').html())+ parseFloat(tprice)).toFixed(2));
                    $('#eprice').html('0.00');
                    //$('#eprice').html((parseFloat($('#shipping-pay').html())).toFixed(2));
                }else{
                    $('#eprice').html((parseFloat($('#shipping-pay').html())+parseFloat(tprice)).toFixed(2));
                }
            }
            else
                window.location.href = '/public/errorAjax?v=3';
          },
          error:function(){
              window.location.href = '/public/errorAjax?v=3';
              return ;
          }
      })
  }else{
      $.ajax({
          type: "POST",
          url: "/order/buycart",
          data:{'select':JSON.stringify(cgs)},
          dataType: "json",
          timeout : 10000,
          success: function(msgObj){
            if(msgObj.ret == 1){
                var data = msgObj.data, allPrice = 0, tprice = 0, html = '', i;
                for(i in data){
                   tprice = (data[i].goods_price*data[i].car_good_num).toFixed(2);
                   allPrice += parseFloat(tprice);
                    html += '<tr class="table-data">'
                    + '<td class="col-1 col-first">'
                    +'<a href="javascript:void(0);" class="order-item-pic dib fl">'
                    + '<img style="margin-top:10px" src=" ' 
                    + data[i].goods_img
                    + '" alt="'+data[i].goods_name+'">'
                    +'</a>'
                    + '<div class="order-item-desc dib">'
                    + '<span class="order-item-name">'+data[i].goods_name+', '+data[i].g_attr_barcode+'</span>'
                    + '<span class="order-item-info">颜色:'+data[i].col+' 尺码:'+data[i].size+'</span>'
                    + '</div>'
                    + '</td>'
                    + '<td class="col-2 tac">'
                    + data[i].goods_price
                    + '</td>'
                    + '<td class="col-3 tac">'
                    + data[i].car_good_num
                    + '</td>'
                    + '<td class="col-4 tac">'
                    + tprice
                    + '</td>'
                    + '</tr>';
                }
                $('#goods-list tbody').html(html);
                $('#tprice').html(allPrice);
                $('#eprice').html((allPrice+parseFloat($('#shipping-pay').html())).toFixed(2));
            }else if(msgObj.ret == 3){
                  window.location.href = '/public/errorAjax?v=2';
                  return false;
            }else
                window.location.href = '/public/errorAjax?v=3';
          },
          error:function(){
              window.location.href = '/public/errorAjax?v=3';
              return ;
          }
      })
  }

  //发票
  $('#ticket').on('click', function(){
      if($('#ticket').is(':checked')){
          $('.ticket').show();
          $('#invoice').trigger('change');
      }else{
          $('#invoice-title').val('');
          $('.ticket').hide();
      }
  })


  $('#invoice').on('change', function(){
      //是否开发票
      if($('#ticket').is(':checked')){
          if($(this).val() == 3){
              $('#invoice-title').val($('.address > .active').find('.address-header > .user-name').html());
          }else
              $('#invoice-title').val('');
      }
  })

  //下单
  $('#cart-go').on('click', function(){
      var $add = $('.address > .active');
      if($add.length != 1){
        alert('请选择一个收货地址');
        return false;
      }
      //收货地址
      var addId = $add.data('id'), is_inv, inv_title = $('#invoice-title').val();
      //是否发票
      if($('#ticket').is(':checked')){
          is_inv = 1;
          if(!inv_title){
            alert('请输入发票抬头');
            return false;
          }

      }else
          is_inv = 3;
      $.ajax({
          type: "POST",
          url: "/order/go",
          data:{
            is_inv:is_inv,
            inv_type:$('#invoice').val(),
            inv_title:inv_title,
            add_id:addId,
            memo:$('#memo').val(),
            select:JSON.stringify(cgs)
          },
          timeout : 10000,
          dataType: "json",
          success: function(msgObj){
            if(msgObj.ret == 1){
              delc('cg');
              cgs = null;
              if(codeFlag == 1)
                window.location.href = '/order';
              else
                window.location.href = '/order/payments';
              return;
            }else if(msgObj.ret == 3){
              alert('您购物车某商品库存不足');
              return false;
            }else
                window.location.href = '/public/errorAjax?v=6';
          },
          error:function(){
               window.location.href = '/public/errorAjax?v=6';
              return ;
          }
      })
  })
})
