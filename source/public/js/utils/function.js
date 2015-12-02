jQuery(function($){

    // 调用所要运行的函数区域
    minHight();
    equalHeight();
    backTop();
    // 延迟加载图片
    var blazy = new Blazy(); // click show, and click hide again
      $("#weixin").click(function(event){ // event 兼容性
          $(".wx-drop").show();
          event.stopPropagation();
      });
      $(document).click(function(){
          $(".wx-drop").hide();
      });

      // mouse over show, and leave hide
      $(".share").hover(function(event){
              $("#share-weibo,#share-qzone").show("slow");
          }, function(){
              $("#share-weibo,#share-qzone").hide("fast");
          }
      );

      // 头部帮助中心购物车显示隐藏
      $(".nav-help").hover(function(event){
              $(this).find("ul").show();
          },function(){
              $(this).find("ul").hide("fast");
          }
      );

    if(location.href.indexOf('/cart') == -1 && location.href.indexOf('/order/confirm') == -1){
      $("#cart").hover(function(event){
          $(this).addClass("nav-bg");
          $(".nav-cart-list").show();
          if($("#nav-cart-list > .cart-box").length == 0){
              $.getJSON("/cart/data", function(data){
                if(data.ret == 3)
                    return;
                var num = data.length, i, html = '';
                if(num){
                    for(i in data){
                      if(i >=5)
                        break;
                      var attrsName = data[i].attrs_names.split(',');
                      html += '<div class="cart-box">'
                        + '<div class="inner">'
                        + '<a class="cart-link-thumb" href="/goods/index/' +data[i].goods_id+ ' "><img class="cart-shoes-thumb vam" style = "width:50px;padding:4px 2px" src=" ' +data[i].goods_img+ ' " alt=" ' +data[i].goods_name+ ' "></a>'
                        + '<div class="cart-sim-info">'
                        + '<div class="brief-view"><a class="cart-link-info" href="/goods/index/' +data[i].goods_id+ ' "><span class="tit"> ' +data[i].goods_name+ '</span><span class="price" style="float:right"> ' + data[i].goods_price + ' x ' + data[i].car_good_num +'</span></a></div>'
                        + '<p class="desc">颜色：<span>'+attrsName[0]+'</span>  尺码：<span>'+attrsName[1]+'码</span></p>'
                        + '</div>'
                        + '</div>'
                        + '</div>';
                    }
                    $('#nav-cart-list').prepend(html);
                    $('#cart-num').html(num);
                    $('#badge').html(num);
                }else{
                    $('#nav-cart-list').prepend('<div class="cart-box"></div>');
                }
              });
          }
          $("#nav-cart-list").show();
      },function(){
        $(this).removeClass("nav-bg");
        $(".nav-cart-list").hide(100);
        $("#nav-cart-list").hide("fast");
      }
    );
  }

    // 最小高度
    function minHight(){
      var minScreenH = 510, // 最小高度
            windowHeight = $(window).height();
      if (windowHeight <= minScreenH){
          windowHeight = minScreenH;
      }
      $('.index-first').css('height', windowHeight); // .index-first是作用对象
      $(window).resize(function(){
          if ($(window).height() <= minScreenH){
              $('.index-first').css('height', minScreenH);
          } else{
              $('.index-first').css('height', windowHeight);
          }
      });
    }

    // 两列等高
    function equalHeight(){
          var sideHeight = $("#side").height(),
              mainHeight = $("#main").height();
          if (sideHeight >= mainHeight){
              $("#main").css("height", sideHeight);
          } else{
              $("#side").css("height", mainHeight);
          };
          $(window).resize(function(){
              equalHeight();
          });
      }

      // 返回顶部
      function backTop(){
          $("#backtop").hide();
          $(window).scroll(function(){
            if ($(this).scrollTop() > 1){
              $("#backtop").fadeIn();
            } else{
              $("#backtop").fadeOut();
            }
          });
          $("#backtop a").click(function(){
            $("html,body").animate({ // 一定要"html,body"，否则会有兼容性问题
              scrollTop: 0
            }, 900);
            if($('.index-nav').html())
              $('.index-nav').show()
            return false;
          });
      }

      // 选择支付方式切换
      $('.pay-ways-change>span').on('click', function(){
          var id = $(this).data('id');
          $(this).addClass("cur").siblings().not("em").removeClass("cur");
          $('#' + id + '-ls').show().siblings().hide();
      })

      //支付
      $('#pay-next').on('click', function(){
          var way = $('input[name = pay-way]:checked').data('way');
          //微信支付
          switch(way){
              case 'wx':
                  location.href = '/pay/wxpay'; 
                  break;
              case 'zfb':
                 location.href = '/pay/alipay'; 
                  break;
              default:
                  alert('请先选择支付方式');
                  return false;
          }
      })
});

