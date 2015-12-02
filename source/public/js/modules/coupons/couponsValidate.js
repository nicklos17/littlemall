define(function(require, exports, module){
    jQuery.extend(jQuery.validator.messages, {
    required: "必选字段",
    remote: "请修正该字段",
    email: "请输入正确格式的电子邮件",
    url: "请输入合法的网址",
    date: "请输入合法的日期",
    dateISO: "请输入合法的日期 (ISO).",
    number: "请输入合法的数字",
    digits: "只能输入整数",
    creditcard: "请输入合法的信用卡号",
    equalTo: "请再次输入相同的值",
    accept: "请输入拥有合法后缀名的字符串",
    maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
    minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
    rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
    range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
    max: jQuery.validator.format("请输入一个最大为{0} 的值"),
    min: jQuery.validator.format("请输入一个最小为{0} 的值")
    });
   // 表单验证默认选项
  $.validator.setDefaults(
    {
      messages: {
        num: {
          required: "请填写数量"
        },
        starTime: {
          required: "请填写开始时间"
        },
        endTime: {
          required: "请填写结束时间"
        },
        name: {
          required: "请填写名称"
        },
        minAmount: {
          required: "请填写最小金额"
        },
        maxAmount: {
          required: "请填写最大金额"
        },
        amount: {
          required: "请填写抵消金额"
        },
        gid: {
          required: "请填写指定商品ID"
        },
      },
    }
    );
// 自定义验证方法
   $.validator.addMethod('ismobile', function(value, element){
    var length = value.length;
    var mobile = /^0?(13[0-9]|15[0-9]|18[0-9]|14[0-9])[0-9]{8}$/; 
    return length == 11 && mobile.exec(value);
  }, '手机格式错误');
   
  $.validator.addMethod('isemail', function(value, element){
    var length = value.length;
    var email = /^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/
;
    return length == 0 || email.exec(value);
  }, '邮箱格式错误');

  $.validator.addMethod('isZipCode', function(value, element) {
    var reg = /^[0-9]{6}$/;
    return reg.exec(value);
  }, '邮政编码格式不正确');

  $.validator.addMethod('isZipCode', function(value, element) {
    var reg = /^[0-9]{6}$/;
    return reg.exec(value);
  }, '邮政编码格式不正确');

  $.validator.addMethod('locSelectorValidate', function(value, element) {
    var $ele = $(element);
    if($ele.hasClass('customed')) {
      var flag = !!value && $ele.parent().is(':visible');
      if(!flag) {
        return false;
      }
      $.each($ele.last('.select-control').siblings('.select-control'), function(){
        var $e = $(this);
        if(!$e.is(':visible')) {
          flag = flag && true;
        } else {
          flag = flag && !!$e.find('select').val();
        }
      });
      return flag;
    } else {
      return !!value;
    }
  }, '请选择省市区');

  // 优惠
  $(".addCoupons-form").validate({
      onKeyUp: false,
      rules: {
        num: {
          required: true,
          number: true
        },
        starTime: {
          required: true,
          date: true
        },
        endTime: {
          required: true,
          date: true
        }
      }
  });

  //优惠券规则
  $(".addCouponsRule-form").validate({
      onKeyUp: false,
      rules: {
        name: {
          required: true,
        },
        minAmount: {
          required: true,
          number: true
        },
        maxAmount: {
          required: true,
          number: true
        },
        amount: {
          required: true,
          number: true
        },
      }
  });

  //云码
  $(".addCode-form").validate({
      onKeyUp: false,
      rules: {
        num: {
          required: true,
          // number: true
        },
        gid: {
          required: true,
        },
        starTime: {
          required: true,
          date: true
        },
        endTime: {
          required: true,
          date: true
        }
      }
      
  });
    // placehholder的显示逻辑
  $('.form').find('.placeHolderWrapper').on('click', function(){
    $(this).find('input, textarea').focus();
  });

  $('.form').find('.placeHolderWrapper input, .placeHolderWrapper textarea').each(function(){
    if($(this).val()) {
      $(this).prev('.inputHint').hide();
    }
    $(this).focus(function(){
      $(this).prev('.inputHint').hide();
    }).blur(function(){
      if(!$(this).val()){
        $(this).prev('span').show();
      }
    });
  });
});