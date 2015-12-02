define(function(){
    function setc(name, value){
        exp = new Date();
        exp.setTime(exp.getTime() + 86400000);
        document.cookie = name + " = "+ escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    }

    $('.table-data').hover(
        function(){
            $(this).find('img').addClass('active');
            $(this).find('.order-item-name').addClass('active');
        },
        function(){
            $(this).find('img').removeClass('active');
            $(this).find('.order-item-name').removeClass('active');
        }
    )

    $('.table-data .close').on('click', function(){
          var $c = $(this).parent().parent(), num = parseInt($('#badge').html()) - 1;
          $.ajax({
              type: "POST",
              url: "/cart/del",
              data:{'gid':$c.find('.gid').data('id'), 'attrs-ids':$c.find('.aid').data('id')},
              timeout : 10000,
              dataType: "json",
              success: function(msgObj){
                if(msgObj.ret == 1){
                    window.location.href = '';
                    // $c.remove();
                    // if(num >= 0)
                    //     $('#badge').html(num);
                    // else
                    //    $('#badge').html(0);
                    // $('#count-item').html(parseInt($('#count-item').html())-1);
                    // if($('.table-data').length == 0)
                    //     window.location.href = '';
                }else
                    window.location.href = '/public/errorAjax?v=2';
              },
              error:function(){
                  window.location.href = '';
              }
          })
    })

    $('.all-check').on('click', function(){
        if($(this).is(':checked')){
            $('.all-check').prop('checked', true);
            $(".table>tbody input[type = 'checkbox']").each(function(){
                if(!this.checked)
                    $(this).trigger('click');
            });
        }else{
            $('.all-check').prop('checked', false);
            $(".table>tbody input[type = 'checkbox']").each(function(){
                if(this.checked)
                    $(this).trigger('click');
            });
        }
    })

    $(".table>tbody input[type = 'checkbox']").on('click', function(){
        var flag = 1, $p = $(this).parent(), price = parseFloat($p.next().next().html()),
        num = parseInt($p.parent().find('.input-num').val());
        $('#count-item').html($(".table>tbody input[type = 'checkbox']:checked").length);
        if($(this).is(':checked')){
             $(".table>tbody input[type = 'checkbox']").each(function(){
                if(!$(this).is(':checked')){
                    flag = 0;
                    return false;
                }
            });
            if(flag == 1)
                $('.all-check').prop('checked', true);
            $('#total-p').html((parseFloat($('#total-p').html())+price*num).toFixed(2));
        }else{
            $('.all-check').prop('checked', false);
            $('#total-p').html((parseFloat($('#total-p').html())-price*num).toFixed(2));
        }
    })

    $('.table>tbody .num-add ').on('click', function(){
        var $t = $(this), $n = $t.parent(), $pn = $n.parent(), $tpn = $pn.parent(), $input = $n.find('.input-num'), num = $input.val(),
        price = parseFloat($n.parent().prev('td').html()), $tprice = $n.parent().next('td');
        if($t.hasClass('disabled'))
          return false;
        num ++;
        if(!/^[1-9][0-9]*$/.test(num) || !/^[1-9][0-9]*\.[0-9]{2}$/.test(price.toFixed(2))){
            window.location.href = '';
            return;
        }
        $.ajax({
            type: "POST",
            url: "/cart/incr",
            timeout : 10000,
            data:{'gid':$tpn.find('.gid').data('id'), 'attrs-ids':$tpn.find('.aid').data('id')},
            dataType: "json",
            success: function(msgObj){
              if(msgObj.ret == 1){
                  $input.val(num);
                  $tprice.html((num*price).toFixed(2));
                  if($pn.parent().find('input[type=checkbox]').prop('checked')){
                      $('#total-p').html((parseFloat($('#total-p').html())+price).toFixed(2));
                  }
              }else if(msgObj.ret == 3){
                  $n.find('.warn-stock').show();
                  $t.addClass('disabled');
                  return;
              }else if(msgObj.ret == 5){
                  $t.addClass('disabled');
                  return;
              }
              else{
                  window.location.href = '/public/errorAjax?v=2';
              }
            },
            error:function(){
                window.location.href = '';
            }
        })
    })

    $('.table>tbody .num-reduce').on('click', function(){
        var $n = $(this).parent(), $pn = $n.parent(), $tpn = $pn.parent(), $input = $n.find('.input-num'), num = $input.val(),
        price = parseFloat($pn.prev('td').html()), $tprice = $pn.next('td');
        if(!/^[1-9][0-9]*$/.test(num) || !/^[1-9][0-9]*\.[0-9]{2}$/.test(price.toFixed(2))){
            window.location.href = '';
            return;
        }
        num --;
        if(num <= 0)
            return false;
        $input.val(num);
        $tprice.html((num*price).toFixed(2));
        if($pn.parent().find('input[type=checkbox]').prop('checked')){
            $('#total-p').html((parseFloat($('#total-p').html())-price).toFixed(2));
        }

        $.ajax({
            type: "POST",
            url: "/cart/decr",
            data:{'gid':$tpn.find('.gid').data('id'), 'attrs-ids':$tpn.find('.aid').data('id')},
            timeout : 10000,
            dataType: "json",
            success: function(msgObj){
                if(msgObj.ret != 1){
                    window.location.href = '';
                    return ;
                }
                if($n.find('.num-add').hasClass('disabled'))
                    $n.find('.num-add').removeClass('disabled');
                $n.find('.warn-stock').hide();
            },
            error:function(){
                window.location.href = '';
            }
        })
    })

    $('#batch-del').on('click', function(){
        var gids = [], attrs = [];
         $(".table>tbody input[type = 'checkbox']").each(function(){
            var $c = $(this).parent().parent();
            if($(this).is(':checked')){
                gids.push($c.find('.gid').data('id'));
                attrs.push($c.find('.aid').data('id'));
            }
        });
          $.ajax({
              type: "POST",
              url: "/cart/batchdel",
              data:{'gids':gids, 'attrs-ids':attrs},
              timeout : 10000,
              dataType: "json",
              success: function(msgObj){
                  if(msgObj.ret == 1)
                      window.location.href = '';
                   else
                      window.location.href = '/public/errorAjax?v=3';
                    return;
              },
              error:function(){
                 window.location.href = '';
              }
          })
    })

    $('#expired-del').on('click', function(){
        $.ajax({
            type:"POST",
            url:"/cart/expired",
            timeout : 10000,
            dataType:"json",
            success:function(data){
                if(data.ret === 1)
                    window.location.href = '';
                else
                    window.location.href = '/public/errorAjax?v=5';
            }
        });

    })

    $('#check-out').on('click', function(){
        var $c = $(".table>tbody input[type = 'checkbox']"), num = 0, cv = {};
        $(".table>tbody input[type = 'checkbox']").each(function(){
            if($(this).is(':checked')){
                var $p = $(this).parent().parent(), g =  $p.find('.gid').data('id'),
                a = $p.find('.aid').data('id');
              if(!/^[1-9][0-9]*$/.test(g) || !/^[1-9][0-9]*,[1-9][0-9]*$/.test(a)){
                  window.location.href = '';
                  return false;
              }
                num ++;
                cv[num] = {"g": g, "a": a};
                setc('cg', JSON.stringify(cv));
                window.location.href = '/order/confirm/';
            }
          })
        if(num ==0)
            return false;
    });
    $('.table>tbody input[type = checkbox]').prop('checked', false);
    $('.all-check').prop('checked', false);
    $('.all-check').eq(1).trigger('click');
})